<?php

namespace model\Manager ;

use Exception;
use model\Abstract\AbstractMapping;
use model\Interface\InterfaceManager;
use model\Mapping\ArticleMapping;
use model\trait\TraitRunQuery;
use model\OurPDO;


class ArticleManager implements InterfaceManager{

    use TraitRunQuery;
    private ?OurPDO $connect = null;

    public function __construct(OurPDO $db){
        $this->connect = $db;
    }


    public function selectAll(): ?array  // sauf les noms des tables, ceci est souvent similaire - faut que remplacer article_ par comment_ donc sans doute possible de mettre ailleurs
    {
        $sql = "SELECT * FROM `article`
         ORDER BY `article_date_create` DESC";
        
        $array = $this->runQuery($sql); // utilisant mon Trait pour réduire le répetition du code

        if (!is_array($array)) throw new Exception("Articles introuvable suite d'une erreur"); // au cas où de problème
        $arrayArticles = [];
        foreach($array as $value){
            $arrayArticles[] = new ArticleMapping($value);
        }
        return $arrayArticles;
    }

/* 
*
*   Tout le reste ici est pour satisfait le Interface pendant que je crée les autres fonctions
*
*/


    // RECUEPRATION D'UN ARTICLE VIA ID 
    public function selectOneById(int $id): null|string|ArticleMapping
    {

        $sql     = "SELECT * 
                    FROM `article` 
                    WHERE `article_id`= ?";
        $getStmt = $this->connect->prepare($sql);
        $getStmt->bindValue(1,$id, OurPDO::PARAM_INT);

        try{
            $getStmt->execute();
            if($getStmt->rowCount()===0) return null;
                $result = $getStmt->fetch(OurPDO::FETCH_ASSOC);
                $result = new ArticleMapping($result);
                $getStmt->closeCursor();
            return $result;
        }catch(Exception $e){
            return $e->getMessage();
        }
        
    }

    public function insert(AbstractMapping $mapping) {

    }
    public function update(AbstractMapping $mapping) {

    }


/*
    // mise à jour d'un commentaire
    public function update(AbstractMapping $mapping): bool|string
    {

        // requête préparée
        $sql = "UPDATE `comment` SET `comment_text`=?, `comment_date_update`=? WHERE `comment_id`=?";
        // mise à jour de la date de modification
        $mapping->setCommentDateUpdate(date("Y-m-d H:i:s"));
        $prepare = $this->connect->prepare($sql);

        try{
            $prepare->bindValue(1,$mapping->getCommentText());
            $prepare->bindValue(2,$mapping->getCommentDateUpdate());
            $prepare->bindValue(3,$mapping->getCommentId(), OurPDO::PARAM_INT);

            $prepare->execute();

            $prepare->closeCursor();

            return true;

        }catch(Exception $e){
            return $e->getMessage();
        }
        
    }


    // insertion d'un commentaire
    public function insert(AbstractMapping $mapping): bool|string
    {

        // requête préparée
        $sql = "INSERT INTO `comment`(`comment_text`,`user_user_id`,`article_article_id`)  VALUES (?,?,?)";
        $prepare = $this->connect->prepare($sql);

        try{
            $prepare->bindValue(1,$mapping->getCommentText());
            $prepare->bindValue(2,1, OurPDO::PARAM_INT);
            $prepare->bindValue(3,1, OurPDO::PARAM_INT);

            $prepare->execute();

            $prepare->closeCursor();

            return true;

        }catch(Exception $e){
            return $e->getMessage();
        }
    }
*/
    // SUPPRESSION D'UN ARTICLE
    public function delete(int $id): bool|string
    {
        $sql     = "DELETE FROM `article` 
                    WHERE `article_id`=?";
        $delStmt = $this->connect->prepare($sql);

        try{
            $delStmt->bindValue(1,$id, OurPDO::PARAM_INT);
            $delStmt->execute();
            $delStmt->closeCursor();

            return true;

        }catch(Exception $e){
            return $e->getMessage();
        }
        
    }

}

