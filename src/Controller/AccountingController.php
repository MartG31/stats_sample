<?php

namespace App\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use App\Entity\BankAccount;
use App\Entity\BankOperation;
use App\Entity\BankCategory;
use App\Entity\BankLabel;

use App\Form\BankAccountType;
use App\Form\BankCategoryType;
use App\Form\BankLabelType;
use App\Form\LinkLabelType;
use App\Form\LoadOperationType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AccountingController extends MasterController
{
	private $upload_dir_csv = 'uploads/csv/';

    // -----

    public function index(Request $request, ObjectManager $manager) {

        $user = $this->getUser();

        $countingOperations = $manager->getRepository(BankAccount::class)->countMyOperations($user);

        if($countingOperations['countOperations'] > 0) {
            $progress = floor($countingOperations['countIdentified'] / $countingOperations['countOperations'] * 100);
        }
        else { $progress = 0; }


        return $this->render('accounting/index.html.twig', [
        	'accounts' => $manager->getRepository(BankAccount::class)->findMyAccountsWithAssets($user),
            'countAccounts' => $manager->getRepository(BankAccount::class)->countMyAccounts($user),
            'countOperations' => $countingOperations['countOperations'],
            'countIdentified' => $countingOperations['countIdentified'],
            'countUnidentified' => $countingOperations['countUnidentified'],
            'progress' => $progress,
        ]);
    }

    public function upload(Request $request, ObjectManager $manager) {

        $user = $this->getUser();

        // UPLOAD CSV

        $form = $this->createForm(LoadOperationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $upload_errors = [];
            $file = $form['file']->getData();

            // Init MIME Type
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($fileInfo, $file);
            finfo_close($fileInfo);

            $extension = explode('.', $file->getClientOriginalName())[1];
            $allowedMimes = ['text/plain', 'text/csv'];

            // VERIFICATION DU FORMAT DE FICHIER
            if(!in_array($mimeType, $allowedMimes) || $extension != 'csv') {
                $upload_errors[] = 'Le type de fichier est invalide';
            }
            else {
                $dt = new \DateTime();
                $fileName = $dt->format('Ymd-His').'.'.$extension;
                $finalPath = $this->upload_dir_csv.$fileName;
                $file->move($this->upload_dir_csv, $fileName);
            }
            // echo '</pre>';

            if(count($upload_errors) == 0) {

                $upload_success = true;

                // RECUPERATION DES DONNEES
                $csv = fopen($finalPath,'r');
                $tab = [];
                while($data = fgetcsv($csv, 1024, ';')) {
                    $tab[] = $data;
                }

                // TRAITEMENT DES DONNEES
                $countOpe = 0;
                $countNewAcc = 0;
                $countNewOpe = 0;


                foreach ($tab as $key => $lig) {

                    if($key != 0) {
                        $acc_exists = $manager->getRepository(BankAccount::class)->findOneBy([
                            'user' => $user,
                            'accountNumber' => $lig[0],
                        ]);

                        if(empty($acc_exists)) {

                            $acc = new BankAccount();
                            $acc->setUser($user);
                            $acc->setAccountNumber($lig[0]);

                            $manager->persist($acc);
                            $manager->flush();

                            $countNewAcc++;
                        }
                        else {
                            $acc = $acc_exists;
                        }

                        $ope_exists = $manager->getRepository(BankOperation::class)->findOneBy([
                            'user' => $user,
                            'dateCompta' => new \DateTime($lig[1]),
                            'ref' => $lig[4],
                            'amount' => $lig[6],
                        ]);

                        if(empty($ope_exists)) {

                            $ope = new BankOperation();
                            $ope->setUser($user);
                            $ope->setAcc($acc);
                            $ope->setDateCompta(new \DateTime($lig[1]));
                            $ope->setDescription($lig[3]);
                            $ope->setRef($lig[4]);
                            $ope->setAmount($lig[6]);

                            $manager->persist($ope);
                            $manager->flush();

                            $countNewOpe++;
                        }

                        $countOpe++;
                    }
                }

            }

        }

        return $this->render('accounting/upload-csv-file.html.twig', [
            'lo_form' => $form->createView(),
            'tab' => $tab ?? [],
            'upload_errors' => $upload_errors ?? [],
            'upload_success' => $upload_success ?? false,
            'countOpe' => $countOpe ?? false,
            'countNewAcc' => $countNewAcc ?? false,
            'countNewOpe' => $countNewOpe ?? false,
        ]);
    }

    public function export(ObjectManager $manager) {

        $user = $this->getUser();
        $tab = $manager->getRepository(BankOperation::class)->findMyCompleteAccountingData($user);



        return $this->render('accounting/export.html.twig', [
            'tab' => $tab,
        ]);

    }

    // ACCOUNT

    public function viewAccount(BankAccount $acc, ObjectManager $manager) {

    	$opes = $manager->getRepository(BankOperation::class)->findBy([
            'user' => $this->getUser(),
    		'acc' => $acc
    	], [
    		'dateCompta' => 'DESC'
    	]);

    	return $this->render('accounting/view-account.html.twig', [
        	'acc' => $acc,
        	'opes' => $opes,
        ]);
    }

    public function formAccount(BankAccount $acc = null, Request $request, ObjectManager $manager) {

        if(!$acc) {
            $acc = new BankAccount();
            $acc->setUser($this->getUser());
        }

        $form = $this->createForm(BankAccountType::class, $acc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($acc);
            $manager->flush();

            return $this->redirectToRoute('index_accounting');
        }
        
        return $this->render('accounting/form-account.html.twig', [
            'acc_form' => $form->createView(),
            'edit_mode' => $acc->getId() != null
        ]);
    }

    public function deleteAccount() {    	
    }

    // CATEGORIES

    public function indexCategories(ObjectManager $manager) {

        $user = $this->getUser();

        return $this->render('accounting/index-categories.html.twig', [
            'cats' => $manager->getRepository(BankCategory::class)->findMyCatsWithAssets($user),
            'labcats' => $manager->getRepository(BankLabel::class)->findMyLabelsWithCategories($user),
        ]);
    }

    public function ajaxCatColor(Request $request, ObjectManager $manager) {

        $user = $this->getUser();

        $cat = $manager->getRepository(BankCategory::class)->find($request->request->get('cat_id'));
        $cat->setColor($request->request->get('cat_color'));

        $manager->flush();

        return new JsonResponse([
            'cat_id' => $cat->getId(),
            'cat_label' => $cat->getLabel(),
            'cat_color' => $cat->getColor(),
        ]);
    }

    public function formCategory(BankCategory $cat = null, Request $request, ObjectManager $manager) {

        if(!$cat) {
            $cat = new BankCategory();
            $cat->setUser($this->getUser());
        }

        $form = $this->createForm(BankCategoryType::class, $cat);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $manager->persist($cat);
            $manager->flush();

            return $this->redirectToRoute('index_categories');
        }

        return $this->render('accounting/form-category.html.twig', [
            'cat_form' => $form->createView(),
            'edit_mode' => $cat->getId() != null
        ]);
    }

    public function deleteCategory(BankCategory $cat, ObjectManager $manager) {

        $manager->remove($cat);
        $manager->flush();

        return $this->redirectToRoute('index_categories');

    }

    // LABELS

    public function formLabel(BankLabel $lab = null, Request $request, ObjectManager $manager) {

    	if(!$lab) {
            $lab = new BankLabel();
            $lab->setUser($this->getUser());
        }

        $form = $this->createForm(BankLabelType::class, $lab);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($lab);
            $manager->flush();

            return $this->redirectToRoute('index_categories');
        }

        return $this->render('accounting/form-label.html.twig', [
            'lab_form' => $form->createView(),
            'edit_mode' => $lab->getId() != null
        ]);
    }

    public function deleteLabel() {
    }

    public function linkLabel(BankOperation $ope, Request $request, ObjectManager $manager) {

    	$form = $this->createForm(LinkLabelType::class, $ope);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($ope);
            $manager->flush();

            return $this->redirectToRoute('view_account', ['id' => $ope->getAcc()->getId()]);
        }
        
        return $this->render('accounting/link-label.html.twig', [
            'link_form' => $form->createView(),
            'ope' => $ope,
        ]);
    }

    public function unlinkLabel() {
    }

    public function searchLabels(Request $request, ObjectManager $manager) {

        $user = $this->getUser();

        // HOLDING REQUEST

        if(!empty($_POST)) {

            $post = array_map('trim', array_map('strip_tags', $_POST));
            $errors = [];

            if(!isset($post['action'])) {
                $errors[] = 'Le formulaire n\'a pas pu être identifié.';
            }
            else if(empty($ref = $manager->getRepository(BankOperation::class)->find($post['action']))) {
                $errors[] = 'L\'opération de référence est incorrecte.';
            }
            
            if(count($errors) == 0) {

                $toLabelize = [];

                foreach ($post as $key => $val) {
                    
                    if(is_numeric($key)) {
                        if($val == 'on' && !empty($comp = $manager->getRepository(BankOperation::class)->find($key))) {
                            $toLabelize[] = $comp;
                        }
                    }
                }

                $label = $ref->getLab();
                foreach ($toLabelize as $ope) {
                    $ope->setLab($label);

                    $manager->persist($ope);
                    $manager->flush();
                }

                // echo '<pre class="alert alert-info">';
                // print_r($ref->getRef());
                // echo '</pre>';

            }
        }

        // SEARCHING LABELS

        $identif = $manager->getRepository(BankOperation::class)->findMyIdentifiedOpes($user);
        $unident = $manager->getRepository(BankOperation::class)->findMyUnidentifiedOpes($user);

        $suggestGroups = [];

        // echo '<pre class="alert alert-info">';
        foreach ($identif as $ref) {
            // echo '===================================================================================<br>';
            // print_r($ref);

            $refDesc = $ref['description'];

            $refSplit = preg_split('/[ -:*0123456789]/', $refDesc, 0, PREG_SPLIT_NO_EMPTY);

        // echo '</pre>';

            $refSplitFiltered = array_filter($refSplit, function($v){
                if(strlen($v) > 1) { return $v; }
            });
            // print_r($refSplitFiltered);

            $refExpl = explode(" ",$refDesc);

            $refWords = array_filter($refExpl, function($v){
               return trim($v);
            });

            // print_r($refWords);

            $suggests = [];

            foreach ($unident as $comp) {
                $compDesc = $comp['description'];
                $cpt = 0;
                foreach ($refSplitFiltered as $word) {
                    if(strpos($compDesc, $word)) {
                        $cpt++;
                    }
                }
                $percent = round((($cpt / count($refWords))*100));
                
                if($percent > 60) {
                    // echo '-------------------------<br>';
                    // echo $compDesc.'<br>';
                    // echo $cpt.'/'.count($refWords).'<br>';
                    // echo $percent.'<br>';

                    $suggests[] = [
                        'ope' => $comp,
                        'percent' => $percent
                    ];
                }
            }

            if(count($suggests) > 0) {
                $suggestGroups[] = [
                    'ref' => $ref,
                    'suggests' => $suggests
                ];
            }
        }
        // echo '</pre>';

        return $this->render('accounting/search-labels.html.twig', [
            'suggestGroups' => $suggestGroups,
        ]);
    }

    // STATISTICS

    public function indexStatistics(Request $request, ObjectManager $manager) {

        $user = $this->getUser();

        // GET SESSION

        $this->session = new Session();

        $session = [
            'mode' => $this->session->get('mode'),
            'category' => $this->session->get('category'),
            'label' => $this->session->get('label'),
            'account' => $this->session->get('account'),
            'min_date' => $this->session->get('min_date'),
            'max_date' => $this->session->get('max_date'),
        ];

        $all = $this->session->all();

        $filters = $session;

        // DEFAULT

        if ($filters['mode'] == null) {
            $filters['mode'] = 'global';
        }
        elseif ($filters['mode'] == 'category') {

            if($filters['category'] == null) {
                $filters['mode'] = 'global';
            }
        }
        elseif ($filters['mode'] == 'label') {

            if($filters['label'] == null) {
                $filters['mode'] = 'global';
            }
        }

        if ($filters['min_date'] == null || $filters['max_date'] == null) {
            $filters['min_date'] = '2019-10-01';
            $filters['max_date'] = '2020-01-31';
        }

        // CHARTS

        switch ($filters['mode']) {

            case 'global':

                $charts = [
                    'dailyTrace',
                    'monthlyBalance',
                    'creditRepartition',
                    'debitRepartition',
                ];

                break;

            case 'category':

                $currCategory = $manager->getRepository(BankCategory::class)->find($filters['category']);
                // $checkOperationType = $manager->getRepository(BankOperation::class)->checkOperationType($user, $filters, 'category');

                $charts = [
                    'creditHistory',
                    'debitHistory',
                    'creditRepartition',
                    'debitRepartition',
                ];

                break;

            case 'label':

                $currCategory = $manager->getRepository(BankCategory::class)->find($filters['category']);
                $currLabel = $manager->getRepository(BankLabel::class)->find($filters['label']);
                // $checkOperationType = $manager->getRepository(BankOperation::class)->checkOperationType($user, $filters, 'label');

                $charts = [
                    'creditHistory',
                    'debitHistory',
                ];

                break;
        }

        return $this->render('accounting/index-statistics.html.twig', [
            'all' => $all,
            'session' => $session,
            'filters' => $filters,
            'charts' => $charts,
            'accounts' => $manager->getRepository(BankAccount::class)->findMyAccountsWithAssets($user),
            'currCategory' => $currCategory ?? null,
            'currLabel' => $currLabel ?? null,
            'checkOperationType' => $checkOperationType ?? null,
        ]);
    }


    // 
    // 
    // AJAX DATA
    // 

    public function ajaxStatsData(Request $request, ObjectManager $manager) {

        $user = $this->getUser();

        $token = $request->request->get('token');
        $charts = $request->request->get('charts');
        
        $filters = [
            'mode' => $request->request->get('mode'),
            'category' => $request->request->get('category'),
            'label' => $request->request->get('label'),
            'account' => $request->request->get('account'),
            'min_date' => $request->request->get('min_date'),
            'max_date' => $request->request->get('max_date'),
        ];

        $infos = $manager->getRepository(BankOperation::class)->findMyDataInfos($user, $filters);

        foreach ($charts as $chart) {

            switch ($chart) {

                case 'dailyTrace':
                    $data[$chart] = $manager->getRepository(BankOperation::class)->findMyDailyTrace($user, $filters);
                    break;

                case 'monthlyBalance':
                    $data[$chart] = $manager->getRepository(BankOperation::class)->findMyMonthlyBalance($user, $filters);
                    break;

                case 'creditRepartition':
                    $data[$chart] = $manager->getRepository(BankOperation::class)->findMyRepartition($user, $filters, 'credit');
                    break;

                case 'debitRepartition':
                    $data[$chart] = $manager->getRepository(BankOperation::class)->findMyRepartition($user, $filters, 'debit');
                    break;

                case 'creditHistory':
                    $data[$chart] = $manager->getRepository(BankOperation::class)->findMyHistory($user, $filters, 'credit');
                    break;

                case 'debitHistory':
                    $data[$chart] = $manager->getRepository(BankOperation::class)->findMyHistory($user, $filters, 'debit');
                    break;
            }
        }

        return new JsonResponse([
            'token' => $token,
            'infos' => $infos,
            'data' => $data,
        ]);
    }


    // 
    // 
    // AJAX SESSION
    // 

    public function ajaxStatsSession(Request $request) {

        $this->session = new Session();
        $params = $request->request->all();
        $session = [];

        foreach ($params as $key => $val) {
            $this->session->set($key, $val);
            $session[$key] = $this->session->get($key);
        }

        return new JsonResponse($session);
    }
}
