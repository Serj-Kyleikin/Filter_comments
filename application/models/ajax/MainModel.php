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
        $id['id'] =  substr($key, 2, -2);

        try {

            $getUser = $this->connection->prepare('SELECT login FROM plugin_users_registered WHERE id =:id');
            $getUser->execute($id);
            $user = $getUser->fetch(PDO::FETCH_ASSOC);

        } catch(\PDOException $e) {
            $this->log->logErrors($e, 1);
        }

        // Добавление данных в БД

        $data['text'] = htmlspecialchars($_POST['text']);
        $data['user_id'] = $id['id'];

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
        $data['login'] = $user['login'];

        $this->cache->delete('page_main.tmp');

        echo json_encode($data);
    }

    // Получение авторов

    public function getLogins() {

        // Точное совпадение логина

        $correct['login'] = htmlspecialchars($_POST['login']);

        try {

            $getCorrect = $this->connection->prepare("SELECT user_id, login FROM comments as c JOIN plugin_users_registered as r ON r.id = c.user_id WHERE login =:login");
            $getCorrect->execute($correct);
            $rowCorrect = $getCorrect->fetch(PDO::FETCH_ASSOC);

        } catch(\PDOException $e) {
            $this->log->logErrors($e, 1);
        }

        $response = (isset($rowCorrect['login'])) ? $rowCorrect : false;

        // Логин внутри длинных логинов

        $data['login'] = "^" . htmlspecialchars($_POST['login']);

        // Получаем авторов с максимальным количеством комментариев к данной статье

        try {

            $getLogin = $this->connection->prepare("SELECT user_id, login, count(user_id) as n FROM comments as c JOIN plugin_users_registered as r ON  c.user_id = r.id WHERE login REGEXP :login GROUP BY login ORDER BY n DESC LIMIT 0,3");
            $getLogin->execute($data);

            $row = $getLogin->fetchAll(PDO::FETCH_ASSOC);

        } catch(\PDOException $e) {
            $this->log->logErrors($e, 1);
        }

        if($response == false) $response = (isset($row[0]['login'])) ? $row : false; 
        else {

            if(isset($row[0]['login']) and count($row) != 1) {

                // Поиск точного совпадения логина среди логинов с наибольшим количеством постов

                $result = array_filter($row[0], function($v) use ($response) {
                    return $v == $response['login'];
                });

                // Если не найдено, то добавляется в подборку (актуально для коротких логинов, с малым количеством комментариев)

                if(!isset($result['login'])) {

                    $row = array_reverse($row);
                    $row[] = $response;
                    $response = array_reverse($row);        // Первым точное совпадение, затем по количеству комментариев

                } else $response = $row;

            } elseif(isset($row[0]['login']) and count($row) == 1) $response = $row;
        }

        echo json_encode($response);
    }

    // Кнопка показать ещё

    public function showComments() {

        try {

            $query = "SELECT user_id, text, date, login FROM comments as c JOIN plugin_users_registered as r ON c.user_id = r.id";
            if(isset($_POST['filter'])) $query .= " WHERE login = :login";      // Условие из фильтра
            $query .= " ORDER BY date DESC LIMIT :from,4";

            $getInfo = $this->connection->prepare($query);
            $getInfo->bindValue(':from', (int)$_POST['from'], PDO::PARAM_INT);
            if(isset($_POST['filter'])) $getInfo->bindValue(':login', $_POST['filter']);
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