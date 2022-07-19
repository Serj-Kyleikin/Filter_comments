<?

namespace application\models\ajax;

use application\core\Model;
use PDO;

class MainModel extends Model {

    // Добавление комментария

    public function sentComment() {

        // Получение пользователя

        $secret = substr(explode('_', $_COOKIE['user'])[0], 0, -2);
        $key = (int)$secret / 3;
        $id['user_id'] =  substr($key, 2, -2);

        try {

            $getUser = $this->connection->prepare('SELECT name FROM plugin_users_personal WHERE user_id =:user_id');
            $getUser->execute($id);
            $user = $getUser->fetch(PDO::FETCH_ASSOC);

        } catch(\PDOException $e) {
            $this->log->logErrors($e, 1);
        }

        // Добавление данных в БД

        $data['text'] = htmlspecialchars($_POST['text']);
        $data['user_id'] = $id['user_id'];

        try {

            $this->connection->beginTransaction();
            $this->connection->exec("LOCK TABLES comments WRITE");
            $insert = $this->connection->prepare("INSERT INTO comments(user_id, text, date) VALUES(:user_id, :text, now())");
            $insert->execute($data);
            $this->connection->commit();
            $this->connection->exec("UNLOCK TABLES");

        } catch(\PDOException $e) {
            $this->log->logErrors($e, 1);
        }

        $data['date'] = date("Y-m-d");
        $data['name'] = $user['name'];

        $this->cache->delete('page_main.tmp');

        echo json_encode($data);
    }

    // Получение авторов

    public function getLogins() {

        // Точное совпадение логина

        $correct['name'] = htmlspecialchars($_POST['name']);

        try {

            $getCorrect = $this->connection->prepare("SELECT c.user_id as cid, p.user_id as pid, name FROM comments as c JOIN plugin_users_personal as p ON c.user_id = p.user_id WHERE name =:name");
            $getCorrect->execute($correct);
            $rowCorrect = $getCorrect->fetch(PDO::FETCH_ASSOC);

        } catch(\PDOException $e) {
            $this->log->logErrors($e, 1);
        }

        $response = (isset($rowCorrect['name'])) ? $rowCorrect : false;

        // Логин внутри длинных логинов

        $data['name'] = "^" . htmlspecialchars($_POST['name']);

        // Получаем авторов с максимальным количеством комментариев к данной статье

        try {

            $getName = $this->connection->prepare("SELECT c.user_id as cid, p.user_id as pid, name, count(c.user_id) as n FROM comments as c JOIN plugin_users_personal as p ON c.user_id = p.user_id WHERE name REGEXP :name GROUP BY name ORDER BY n DESC LIMIT 0,3");
            $getName->execute($data);

            $row = $getName->fetchAll(PDO::FETCH_ASSOC);

        } catch(\PDOException $e) {
            $this->log->logErrors($e, 1);
        }

        if($response == false) $response = (isset($row[0]['name'])) ? $row : false; 
        else {

            if(isset($row[0]['name']) and count($row) != 1) {

                // Поиск точного совпадения логина среди логинов с наибольшим количеством постов

                $result = array_filter($row[0], function($v) use ($response) {
                    return $v == $response['name'];
                });

                // Если не найдено, то добавляется в подборку (актуально для коротких логинов, с малым количеством комментариев)

                if(!isset($result['name'])) {

                    $row = array_reverse($row);
                    $row[] = $response;
                    $response = array_reverse($row);        // Первым точное совпадение, затем по количеству комментариев

                } else $response = $row;

            } elseif(isset($row[0]['name']) and count($row) == 1) $response = $row;
        }

        echo json_encode($response);
    }

    // Кнопка показать ещё

    public function showComments() {

        try {

            $query = "SELECT c.user_id as cid, p.user_id as pid, text, date, name FROM comments as c JOIN plugin_users_personal as p ON c.user_id = p.user_id";
            if(isset($_POST['filter'])) $query .= " WHERE name = :name";      // Условие из фильтра
            $query .= " ORDER BY date DESC LIMIT :from,4";

            $getInfo = $this->connection->prepare($query);
            $getInfo->bindValue(':from', (int)$_POST['from'], PDO::PARAM_INT);
            if(isset($_POST['filter'])) $getInfo->bindValue(':name', $_POST['filter']);
            $getInfo->execute();

            $result = $getInfo->fetchAll(PDO::FETCH_ASSOC);

        } catch(\PDOException $e) {
            $this->log->logErrors($e, 1);
        }

        // Маркер show для опреджеления надобности кнопки "Показать ещё"

        if(count($result) == 4) $result[3] = 'show';
        if(!isset($result[0])) $result[0] = false;
        echo json_encode($result);
    }
}