<?

namespace application\models;

use application\core\Model;
use PDO;

class MainModel extends Model {

    // Главная страница

    public function getMain($info) {

        $result['pagination'] = $this->setPagination($info['url'], $info['pagination']);

        $from = ($info['pagination'] == 1) ? 0 : ($info['pagination'] - 1) * $this->pagination;

        // Статьи

        try {

            $getInfo = $this->connection->prepare('SELECT id, text FROM articles ORDER BY id DESC LIMIT :from, :limit');
            $getInfo->bindValue(':from', $from, PDO::PARAM_INT);
            $getInfo->bindValue(':limit', $this->pagination, PDO::PARAM_INT); 
            $getInfo->execute();
            $result['static'] = $getInfo->fetchAll(PDO::FETCH_ASSOC);

        } catch(\PDOException $e) {
            logError($e, 1);
        }

        // Поиск первой статьи из БД в выдаче

        try {

            $getId = $this->connection->prepare('SELECT id FROM articles ORDER BY id LIMIT 1');
            $getId->execute();
            $min = $getId->fetch(PDO::FETCH_ASSOC);

        } catch(\PDOException $e) {
            logError($e, 1);
        }

        $options = '';                          // id постов дял динамического контента

        foreach($result['static'] as $article) {
            $options .= $article['id'] . ',';
            if($article['id'] == $min['id']) $result['pagination']['next'] = false;
        }
        
        $options = trim($options, ',');

        $result['dynamic']['options'] = $options;
        $result['dynamic']['content'] = $this->getMainDynamic($options);

        return $result;
    }

    // Динамичный контент главной страницы

    public function getMainDynamic($options) {

        $ids = explode(',', $options);
        $count = count($ids);

        // Комментарии для статичного текста

        $result = [];

        for($i = 0; $i < $count; $i++) {

            $id['article_id'] = $ids[$i];

            try {

                $getInfo = $this->connection->prepare('SELECT c.user_id as cid, p.user_id as pid, text, date, name FROM comments as c JOIN plugin_users_personal as p ON c.user_id = p.user_id WHERE c.article_id =:article_id ORDER BY c.article_id, date DESC LIMIT 0,4');
    
                $getInfo->execute($id);
                $result[$i] = $getInfo->fetchAll(PDO::FETCH_ASSOC);
    
            } catch(\PDOException $e) {
                logError($e, 1);
            }

            if($result[$i] != '' and count($result[$i]) == 4) $result[$i][3] = 'show';
        }

        return $result;
    }
}
