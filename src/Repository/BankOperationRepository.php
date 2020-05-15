<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\BankOperation;
use App\Entity\BankAccount;
use App\Entity\Bank;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BankOperation|null find($id, $lockMode = null, $lockVersion = null)
 * @method BankOperation|null findOneBy(array $criteria, array $orderBy = null)
 * @method BankOperation[]    findAll()
 * @method BankOperation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BankOperationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BankOperation::class);
    }

// ------------------------------------------------------------------------------------- 
    // 
    // 
    // SEARCH LABELS

    public function findMyIdentifiedOpes(User $user) {

        $q = '  SELECT  c.id as idCat,
                        c.label as labelCat,
                        l.id as idLab, 
                        l.label as labelLab,
                        o.id as idOpe,
                        o.ref as ref,
                        o.date_compta as dateCompta, 
                        round(o.amount,2) as amount,
                        o.description as description

                FROM bank_operation AS o
                LEFT JOIN bank_account AS a ON o.acc_id = a.id 
                LEFT JOIN bank AS b ON a.bank_id = b.id 
                LEFT JOIN bank_label AS l ON o.lab_id = l.id
                LEFT JOIN bank_category AS c ON l.cat_id = c.id
                WHERE o.user_id = :user_id
                AND l.id IS NOT NULL
        ';

        $manager = $this->getEntityManager()->getConnection()->prepare($q);
        $manager->execute([
            ':user_id' => $user->getId(),
        ]);

        return $manager->fetchAll();
    }

    public function findMyUnidentifiedOpes(User $user) {

        $q = '  SELECT  c.id as idCat,
                        c.label as labelCat,
                        l.id as idLab, 
                        l.label as labelLab,
                        o.id as idOpe,
                        o.ref as ref,
                        o.date_compta as dateCompta, 
                        round(o.amount,2) as amount,
                        o.description as description

                FROM bank_operation AS o
                LEFT JOIN bank_account AS a ON o.acc_id = a.id 
                LEFT JOIN bank AS b ON a.bank_id = b.id 
                LEFT JOIN bank_label AS l ON o.lab_id = l.id
                LEFT JOIN bank_category AS c ON l.cat_id = c.id
                WHERE o.user_id = :user_id
                AND l.id IS NULL
        ';

        $manager = $this->getEntityManager()->getConnection()->prepare($q);
        $manager->execute([
            ':user_id' => $user->getId(),
        ]);

        return $manager->fetchAll();
    }

// ------------------------------------------------------------------------------------- 
    // 
    // 
    // STATISTICS
        // DATA INFOS

public function findMyDataInfos(User $user, array $params) {

    // ----------------

    $minDateParam = isset($params['min_date']) && $params['min_date'] != null;
    $maxDateParam = isset($params['max_date']) && $params['max_date'] != null;
    $accParam = isset($params['account']) && $params['account'] != null;

    if($minDateParam) { $minDateFilter = 'AND o.date_compta >= :min_date '; } else { $minDateFilter = ''; }
    if($maxDateParam) { $maxDateFilter = 'AND o.date_compta <= :max_date '; } else { $maxDateFilter = ''; }
    if($accParam) { $accFilter = 'AND a.id = :acc_id '; } else { $accFilter = ''; }

    // ----------------

    switch ($params['mode']) {

        case 'global':
            // $id = 'c.id AS id';
            // $label = 'c.label AS label';
            // $color = 'c.color AS color';
            $modeFilter = '';
            break;

        case 'category':
            // $id = 'l.id AS id';
            // $label = 'l.label AS label';
            // $color = 'c.color AS color';
            $modeFilter = 'AND c.id = :cat_id ';
            break;

        case 'label':
            // $id = 'l.id AS id';
            // $label = 'l.label AS label';
            // $color = 'c.color AS color';
            $modeFilter = 'AND l.id = :lab_id ';
            break;
    }

    // ----------------

    $q = "  SELECT  

                    round(sum(CASE WHEN o.amount > 0 THEN o.amount ELSE NULL END),2) as credit,
                    round(sum(CASE WHEN o.amount < 0 THEN abs(o.amount) ELSE NULL END),2) as debit,
                    count(o.amount) as count
            FROM bank_operation AS o
            LEFT JOIN bank_account AS a ON o.acc_id = a.id 
            LEFT JOIN bank AS b ON a.bank_id = b.id 
            LEFT JOIN bank_label AS l ON o.lab_id = l.id
            LEFT JOIN bank_category AS c ON l.cat_id = c.id
            WHERE o.user_id = :user_id 
            $minDateFilter
            $maxDateFilter
            $accFilter
            $modeFilter
    ";

    // ----------------
    
    $args = [ ':user_id' => $user->getId() ];

    if($minDateParam) { $args[':min_date'] = $params['min_date']; }
    if($maxDateParam) { $args[':max_date'] = $params['max_date']; }
    if($accParam) { $args[':acc_id'] = $params['account']; }

    switch ($params['mode']) {
        case 'category':
            $args[':cat_id'] = $params['category'];
            break;
        case 'label':
            $args[':lab_id'] = $params['label'];
            break;
    }

    // ----------------
    
    $manager = $this->getEntityManager()->getConnection()->prepare($q);
    $manager->execute($args);
    return $manager->fetch();
}




// ------------------------------------------------------------------------------------- 
        // SUIVI (par jour) Solde relatif

    public function findMyDailyTrace(User $user, array $params) {

        // ----------------

        $minDateParam = isset($params['min_date']) && $params['min_date'] != null;
        $maxDateParam = isset($params['max_date']) && $params['max_date'] != null;
        $accParam = isset($params['account']) && $params['account'] != null;

        if($minDateParam) { $minDateFilter = 'AND o.date_compta >= :min_date '; } else { $minDateFilter = ''; }
        if($maxDateParam) { $maxDateFilter = 'AND o.date_compta <= :max_date '; } else { $maxDateFilter = ''; }
        if($accParam) { $accFilter = 'AND a.id = :acc_id '; } else { $accFilter = ''; }

        // ----------------

        $q = "  SELECT  year(o.date_compta) as year,
                        month(o.date_compta) as month,
                        day(o.date_compta) as day,
                        round(sum(o.amount),2) as sum,
                        count(o.id) as countOpe

                FROM bank_operation AS o
                LEFT JOIN bank_account AS a ON o.acc_id = a.id 
                LEFT JOIN bank AS b ON a.bank_id = b.id 
                LEFT JOIN bank_label AS l ON o.lab_id = l.id
                LEFT JOIN bank_category AS c ON l.cat_id = c.id
                WHERE o.user_id = :user_id 
                $minDateFilter
                $maxDateFilter
                $accFilter
                GROUP BY year, month, day
                ORDER BY year ASC, month ASC, day ASC
        ";

        // ----------------

        $args = [ ':user_id' => $user->getId() ];

        if($minDateParam) { $args[':min_date'] = $params['min_date']; }
        if($maxDateParam) { $args[':max_date'] = $params['max_date']; }
        if($accParam) { $args[':acc_id'] = $params['account']; }

        // ----------------

        $manager = $this->getEntityManager()->getConnection()->prepare($q);
        $manager->execute($args);
        return $manager->fetchAll();
    }

        // BALANCE (par mois) Recettes / Dépenses / Balance

    public function findMyMonthlyBalance(User $user, array $params) {

        // ----------------

        $minDateParam = isset($params['min_date']) && $params['min_date'] != null;
        $maxDateParam = isset($params['max_date']) && $params['max_date'] != null;
        $accParam = isset($params['account']) && $params['account'] != null;

        if($minDateParam) { $minDateFilter = 'AND o.date_compta >= :min_date '; } else { $minDateFilter = ''; }
        if($maxDateParam) { $maxDateFilter = 'AND o.date_compta <= :max_date '; } else { $maxDateFilter = ''; }
        if($accParam) { $accFilter = 'AND a.id = :acc_id '; } else { $accFilter = ''; }

        // ----------------

        $q = "  SELECT  year(o.date_compta) as year,
                        month(o.date_compta) as month,
                        round(sum(CASE WHEN o.amount > 0 THEN o.amount ELSE NULL END),2) as credit,
                        round(sum(CASE WHEN o.amount < 0 THEN abs(o.amount) ELSE NULL END),2) as debit,
                        round(sum(o.amount),2) as balance,
                        count(o.id) as countOpe

                FROM bank_operation AS o
                LEFT JOIN bank_account AS a ON o.acc_id = a.id 
                LEFT JOIN bank AS b ON a.bank_id = b.id 
                LEFT JOIN bank_label AS l ON o.lab_id = l.id
                LEFT JOIN bank_category AS c ON l.cat_id = c.id
                WHERE o.user_id = :user_id 
                $minDateFilter
                $maxDateFilter
                $accFilter
                GROUP BY year, month
                ORDER BY year ASC, month ASC
        ";

        // ----------------

        $args = [ ':user_id' => $user->getId() ];

        if($minDateParam) { $args[':min_date'] = $params['min_date']; }
        if($maxDateParam) { $args[':max_date'] = $params['max_date']; }
        if($accParam) { $args[':acc_id'] = $params['account']; }

        // ----------------

        $manager = $this->getEntityManager()->getConnection()->prepare($q);
        $manager->execute($args);
        return $manager->fetchAll();
    }

// ------------------------------------------------------------------------------------- 
        // REPARTITION : Catégorie / Libellé && Credit / Debit

    public function findMyRepartition(User $user, array $params, $type) {

        // ----------------

        $minDateParam = isset($params['min_date']) && $params['min_date'] != null;
        $maxDateParam = isset($params['max_date']) && $params['max_date'] != null;
        $accParam = isset($params['account']) && $params['account'] != null;

        if($minDateParam) { $minDateFilter = 'AND o.date_compta >= :min_date '; } else { $minDateFilter = ''; }
        if($maxDateParam) { $maxDateFilter = 'AND o.date_compta <= :max_date '; } else { $maxDateFilter = ''; }
        if($accParam) { $accFilter = 'AND a.id = :acc_id '; } else { $accFilter = ''; }

        // ----------------
        
        if($type == 'credit') {
            $amount = 'round(sum(CASE WHEN o.amount > 0 THEN o.amount ELSE NULL END),2) as amount';
            // $type = 'credit';
        }
        elseif($type == 'debit') {
            $amount = 'round(sum(CASE WHEN o.amount < 0 THEN abs(o.amount) ELSE NULL END),2) as amount';
            // $type = 'debit';
        }

        // ----------------

        switch ($params['mode']) {

            case 'global':
                $id = 'c.id AS id';
                $label = 'c.label AS label';
                $color = 'c.color AS color';
                $mode = 'GROUP BY c.id ';
                break;

            case 'category':
                $id = 'l.id AS id';
                $label = 'l.label AS label';
                $color = 'c.color AS color';
                $mode = 'AND c.id = :cat_id GROUP BY l.id ';
                break;
        }

        // ----------------

        $q = "  SELECT  $id,
                        $label,
                        $color,
                        $amount
                FROM bank_operation AS o
                LEFT JOIN bank_account AS a ON o.acc_id = a.id 
                LEFT JOIN bank AS b ON a.bank_id = b.id 
                LEFT JOIN bank_label AS l ON o.lab_id = l.id
                LEFT JOIN bank_category AS c ON l.cat_id = c.id
                WHERE o.user_id = :user_id 
                $minDateFilter
                $maxDateFilter
                $accFilter
                $mode
                ORDER BY amount DESC
        ";

        // ----------------
        
        $args = [ ':user_id' => $user->getId() ];

        if($minDateParam) { $args[':min_date'] = $params['min_date']; }
        if($maxDateParam) { $args[':max_date'] = $params['max_date']; }
        if($accParam) { $args[':acc_id'] = $params['account']; }

        switch ($params['mode']) {
            case 'category':
                $args[':cat_id'] = $params['category'];
                break;
        }

        // ----------------
        
        $manager = $this->getEntityManager()->getConnection()->prepare($q);
        $manager->execute($args);
        return $manager->fetchAll();
    }

// ------------------------------------------------------------------------------------- 
        // HISTORY : Catégorie / Libellé && Credit / Debit

    public function findMyHistory(User $user, array $params, $type) {

        // ----------------

        $minDateParam = isset($params['min_date']) && $params['min_date'] != null;
        $maxDateParam = isset($params['max_date']) && $params['max_date'] != null;
        $accParam = isset($params['account']) && $params['account'] != null;

        if($minDateParam) { $minDateFilter = 'AND o.date_compta >= :min_date '; } else { $minDateFilter = ''; }
        if($maxDateParam) { $maxDateFilter = 'AND o.date_compta <= :max_date '; } else { $maxDateFilter = ''; }
        if($accParam) { $accFilter = 'AND a.id = :acc_id '; } else { $accFilter = ''; }

        // ----------------
        
        if($type == 'credit') {
            $amount = 'round(sum(CASE WHEN o.amount > 0 THEN o.amount ELSE NULL END),2) as amount';
        }
        elseif($type == 'debit') {
            $amount = 'round(sum(CASE WHEN o.amount < 0 THEN abs(o.amount) ELSE NULL END),2) as amount';
        }

        // ----------------

        switch ($params['mode']) {

            case 'category':
                $mode = 'AND c.id = :cat_id ';
                break;

            case 'label':
                $mode = 'AND l.id = :lab_id ';
                break;
        }

        // ----------------

        $q = "  SELECT  year(o.date_compta) as year,
                        month(o.date_compta) as month,
                        $amount,
                        count(o.id) as countOpe,
                        c.color as color

                FROM bank_operation AS o
                LEFT JOIN bank_account AS a ON o.acc_id = a.id 
                LEFT JOIN bank AS b ON a.bank_id = b.id 
                LEFT JOIN bank_label AS l ON o.lab_id = l.id
                LEFT JOIN bank_category AS c ON l.cat_id = c.id
                WHERE o.user_id = :user_id
                $minDateFilter
                $maxDateFilter
                $accFilter
                $mode
                GROUP BY year, month
                ORDER BY year ASC, month ASC
        ";

        // ----------------

        $args = [ ':user_id' => $user->getId() ];

        switch ($params['mode']) {

            case 'category':
                $args[':cat_id'] = $params['category'];
                break;

            case 'label':
                $args[':lab_id'] = $params['label'];
                break;
        }

        if($minDateParam) { $args[':min_date'] = $params['min_date']; }
        if($maxDateParam) { $args[':max_date'] = $params['max_date']; }
        if($accParam) { $args[':acc_id'] = $params['account']; }

        // ----------------

        $manager = $this->getEntityManager()->getConnection()->prepare($q);
        $manager->execute($args);
        return $manager->fetchAll();
    }

// ------------------------------------------------------------------------------------- 
    // EXPORT (ALL DATA)

    public function findMyCompleteAccountingData(User $user) {

        // ----------------

        $q = "  SELECT  b.label as labelBank,
                        a.label as labelAcc,
                        a.account_number,
                        o.ref,
                        o.date_compta,
                        o.amount,
                        o.description,
                        l.label as labelLab,
                        c.label as labelCat
                FROM bank_operation AS o
                LEFT JOIN bank_account AS a ON o.acc_id = a.id 
                LEFT JOIN bank AS b ON a.bank_id = b.id 
                LEFT JOIN bank_label AS l ON o.lab_id = l.id
                LEFT JOIN bank_category AS c ON l.cat_id = c.id
                WHERE o.user_id = :user_id
                ORDER BY o.date_compta DESC
        ";

        // ----------------

        $args = [ ':user_id' => $user->getId() ];

        // ----------------

        $manager = $this->getEntityManager()->getConnection()->prepare($q);
        $manager->execute($args);
        return $manager->fetchAll();
    }

}

?>