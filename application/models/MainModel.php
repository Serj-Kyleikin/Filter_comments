<?

namespace application\models;

use application\core\Model;
use PDO;

class MainModel extends Model {

    // Главная страница

    public function getMain($info) {

        // Комментарии для статичного текста

        try {

            $getInfo = $this->connection->prepare('SELECT c.user_id as cid, p.user_id as pid, text, date, name FROM comments as c JOIN plugin_users_personal as p ON c.user_id = p.user_id ORDER BY date DESC LIMIT 0,4');
            $getInfo->execute();
            $result = $getInfo->fetchAll(PDO::FETCH_ASSOC);

        } catch(\PDOException $e) {
            logError($e, 1);
        }

        if($result != '' and count($result) == 4) $result[3] = 'show';

        return $result;
    }
}