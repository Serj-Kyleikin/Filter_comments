<?

namespace application\models\ajax;

use application\core\Model;
use PDO;

class MainModel extends Model {

    // Добавление комментария

    public function sentComment() {

        $id['user_id'] = $this->getID();           // ID пользователя
        $id['article_id'] = $_POST['id'];

        if($_POST['text'] != '') {

            try {

                $getUser = $this->connection->prepare('SELECT a.id, u.name FROM articles as a JOIN plugin_users_personal as u WHERE u.user_id =:user_id and a.id=:article_id ORDER BY a.id DESC, user_id LIMIT 1');
                $getUser->execute($id);
                $user = $getUser->fetch(PDO::FETCH_ASSOC);

            } catch(\PDOException $e) {
                $this->log->logErrors($e, 1);
            }

            // Проверка на существование поста с таким id

            if($user) {

                // Добавление данных в БД

                $data['text'] = htmlspecialchars($_POST['text']);
                $data['user_id'] = $id['user_id'];
                $data['article_id'] = $id['article_id'];

                try {

                    $this->connection->beginTransaction();
                    $this->connection->exec("LOCK TABLES comments WRITE");
                    $insert = $this->connection->prepare("INSERT INTO comments(user_id, article_id, text, date) VALUES(:user_id, :article_id, :text, now())");
                    $insert->execute($data);
                    $this->connection->commit();
                    $this->connection->exec("UNLOCK TABLES");

                } catch(\PDOException $e) {
                    $this->log->logErrors($e, 1);
                }

                $data['date'] = date("Y-m-d");
                $data['name'] = $user['name'];

            } else $data = 0;           // Подмена id поста

        } else $data = 0;               // Подмена id формы

        echo json_encode($data);
    }

    // Получение авторов

    public function getLogins() {

        // Точное совпадение логина

        $correct['name'] = htmlspecialchars($_POST['name']);
        $correct['article_id'] = $_POST['id'];

        try {

            $getCorrect = $this->connection->prepare("SELECT c.user_id as cid, p.user_id as pid, name FROM comments as c JOIN plugin_users_personal as p ON c.user_id = p.user_id WHERE c.article_id =:article_id and name =:name ORDER BY c.article_id DESC, name ASC LIMIT 1");
            $getCorrect->execute($correct);
            $rowCorrect = $getCorrect->fetch(PDO::FETCH_ASSOC);

        } catch(\PDOException $e) {
            $this->log->logErrors($e, 1);
        }

        $response = (isset($rowCorrect['name'])) ? $rowCorrect : false;

        // Логин внутри длинных логинов

        $data['name'] = "^" . htmlspecialchars($_POST['name']);
        $data['article_id'] = $_POST['id'];

        // Получаем авторов с максимальным количеством комментариев к данной статье

        try {

            $getName = $this->connection->prepare("SELECT c.user_id as cid, p.user_id as pid, name, count(c.user_id) as n FROM comments as c JOIN plugin_users_personal as p ON c.user_id = p.user_id WHERE c.article_id =:article_id and name REGEXP :name GROUP BY name ORDER BY c.article_id DESC, name ASC, n DESC LIMIT 0,3");
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

            $query = "SELECT c.user_id as cid, p.user_id as pid, text, date, name FROM comments as c JOIN plugin_users_personal as p ON c.user_id = p.user_id WHERE c.article_id =:article_id";
            if(isset($_POST['filter'])) $query .= " and name = :name";      // Условие из фильтра
            $query .= " ORDER BY c.article_id, date DESC LIMIT :from, 4";

            $getInfo = $this->connection->prepare($query);

            $getInfo->bindValue(':article_id', $_POST['id']);
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