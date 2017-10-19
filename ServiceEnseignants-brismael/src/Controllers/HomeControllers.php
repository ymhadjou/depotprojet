<?php

namespace Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Models\QueriesModel;
use Symfony\Component\Security\Core\User\User;




class HomeControllers {

    /**
     * Page d'accueil du site
     * @return un template twig qui correspond à la page d'accueil
     * */
    function indexPage(Application $app,Request $request) {
        return $app['twig']->render('@views_suivi/base.html.twig', array(
            'cache' => false,
            'auto_reload' => true,
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username')
        ));
    }

    function ajaxCours(Application $app){

        $rech=$_GET['param'];

        //AUTO-COMPLETION
        $querieModel = new QueriesModel($app);
        $tableCours =  $querieModel->getLibelleECFromSearch($app,$rech);

        return json_encode(array_column($tableCours,'LIBELLE_EC'));
    }

    function ajaxEnseignant(Application $app){
        $rech=$_GET['param'];

        $querieModel = new QueriesModel($app);
        $tableEns =  $querieModel->getNomPersonnelFromSearch($app,$rech);

        return json_encode(array_column($tableEns,'NOM'));
    }

    function ajaxStatut(Application $app){
        $rech=$_GET['param'];

        //AUTO-COMPLETION
        $querieModel = new QueriesModel($app);
        $tableStatut =  $querieModel->getStatutFromSearch($app,$rech);
        return json_encode(array_column($tableStatut,'LIBELLE_STAT'));
    }


    function loginPage(Application $app,Request $request) {
        return $app['twig']->render('@views_suivi/login.html.twig', array(
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
        ));
    }


    function adminPage(Application $app) {
        return $app['twig']->render('@views_suivi/admin.html.twig');
    }

    function modifierUser(Application $app, Request $req) {
        $mail=$req->get('email') ;
        $pass=$req->get('pass') ;
       $encodePass=$app['security.encoder.digest']->encodePassword($pass, '') ;


        //REQUETE UPDATE
        $querieModel = new QueriesModel($app);
        $res =$querieModel->updateUserPass( $app,$mail,$encodePass);

       return $app['twig']->render('@views_suivi/admin.html.twig',array('res'=>$res));
    }


    function rechercheStatut(Application $app) {

        $querieModel = new QueriesModel($app);
        $statut = isset($_POST['statut']) ? $_POST['statut'] : false;
        $recherche = false;
        $tableStatut = array();

        if ($statut) {//Si la personne a effectué une recherche
            $tableStatut = $querieModel->getStatutTableSatut($app,$statut);
            $recherche = true;
        }

        //$tableStatut= $query ->getRStatut($app);
        return $app['twig']->render('@views_suivi/recherche.statut.html.twig', array('cache' => false,
                    'auto_reload' => true,
                    'statut' => $tableStatut,
                    'recherche' => $recherche,
        ));
    }

    function rechercheCours(Application $app, $recherche = false) {

        $querieModel =  new QueriesModel($app);
        $cours = isset($_POST['cours']) ? $_POST['cours'] : false;

        $get = $app['request']->query->all();
        //AUTO-COMPLETION
        /*
        foreach ($tableCours as $key => $value)
            $arrayCours[] = $value['LIBELLE_EC'];
        $coursJson = json_encode($arrayCours);
        */

        $tableLsCours =array();
        $Desc_EC = null;


        $coursEscape = (!empty($get)) ? str_replace("'", "''", $get['cours']) : str_replace("'", "''", $cours);
        $annee = $app['annee'];

        if ($cours || !empty($get)) {//Si la personne a effectué une recherche
           $tableCours = $querieModel->getAllFromLibelleEC($app,$coursEscape);
           $tableLsCours = $querieModel->getLigneServiceFromLibelleEC($app,$coursEscape,$annee);
            $Desc_EC = $querieModel->getAllVECFromLibelleEC($app,$coursEscape,$annee);
            $recherche = true;
        }

        return $app['twig']->render('@views_suivi/recherche.cours.html.twig', array('cache' => false,
                    'auto_reload' => true,
                    'cours' => $tableCours,
                    'lignes' => $tableLsCours,
                    'desc_EC' => $Desc_EC[0],
                    'recherche' => $recherche,
                    'coursName' => $coursEscape,
                    'annee' => $app['annee']

        ));
    }

    function rechercheEnseignant(Application $app, $recherche = false) {
        $querieModel = new QueriesModel($app);
        $get = $app['request']->query->all();
        if (!empty($get)) {
            $enseignant = $get['nomEns'];
        } else {
            $enseignant = isset($_POST['enseignant']) ? $_POST['enseignant'] : false;
        }
        $tableEns = array();
        $tableEC = array();
        $nom ="";
        $prenom = "";
        $statut ="";
        $nbhmin ="";
        $nbhmax = "";

        if ($enseignant) {
            $annee = $app['annee'];
            $tableEns = $querieModel->getAllFromEnseignantCHG($app,$enseignant,$annee);
            $tableEC = 	$querieModel->getAllFromEnseignantCours($app,$enseignant,$annee);
            $tableInfEns = $querieModel->getAllFromPersonnelStatut($app,$enseignant);
            $nom = $tableInfEns[0]['NOM'];
            $prenom = $tableInfEns[0]['PRENOM'];
            $statut = $tableInfEns[0]['LIBELLE_STAT'];
            $nbhmin = $tableInfEns[0]['NB_H_MIN'];
            $nbhmax = $tableInfEns[0]['NB_H_MAX'];
            $app['titreImpression'] = "Service de $prenom $nom, $statut";
            $recherche = true;
        }

        return $app['twig']->render('@views_suivi/recherche.enseignant.html.twig', array('cache' => false,
                    'auto_reload' => true,
                    'load_chg' => $tableEns,
                    'load_ec' => $tableEC,
                    'prof' => $recherche,
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'statut' => $statut,
                    'nbhmin' => $nbhmin,
                    'nbhmax' => $nbhmax
        ));
    }

    function modif(Application $app) {
        $querieModel = new QueriesModel($app);

        $tableChg = $querieModel->getLigneServiceCHG($app);
        $tableEC = $querieModel->getLigneServiceEC($app);

        $allGroupe = $querieModel->getGroupe($app);
        $allPersonnel = $querieModel->getPersonnel($app);
        $allCharge = $querieModel->getCharge($app);
        $allMatiere = $querieModel->getMatiere($app);

        return $app['twig']->render('@views_suivi/modif.html.twig', array('cache' => false,
                    'auto_reload' => true,
                    'chg' => $tableChg,
                    'ec' => $tableEC,
                    'groupe' => $allGroupe,
                    'personnel' => $allPersonnel,
                    'charge' => $allCharge,
                    'matiere' => $allMatiere
        ));
    }

    function modifAjaxEC(Application $app) {

        $id = array("ID_LIGNE" => $_POST['id_ligne']);
        $aModif = $_POST;
        foreach ($aModif as $key => $value) {
            if ($value == -1) {
                unset($aModif[$key]);
            }
        }
        unset($aModif['id_ligne']);
        $app['db']->update("LIGNE_SERVICE", $aModif, $id);
        return 1;
    }

    function modifAjaxChg(Application $app) {

        $id = array("ID_LIGNE" => $_POST['id_ligne']);
        $aModif = $_POST;
        foreach ($aModif as $key => $value) {
            if ($value == -1) {
                unset($aModif[$key]);
            }
        }
        unset($aModif['id_ligne']);
        $app['db']->update("LIGNE_SERVICE", $aModif, $id);
        return 1;
    }

    function ajoutLigne(Application $app) {
        $query_model = new QueriesModel($app);

        $var = $_POST;
        foreach ($var as $key => $value) {
            if (empty($value)) {
                unset($var[$key]);
            }
        }

        //Si on ajoute un EC
        if ($_POST['type'] == 1) {
            unset($var["FID_CHGFCT"]);
        }
        //Si on ajoute une charge de fonction
        if ($_POST['type'] == 2) {
            unset($var["FID_EC"]);
            unset($var["H_CM"]);
            unset($var["H_EAD"]);
            unset($var["H_TP"]);
        }
        unset($var["type"]);

        //On recupere le prochain ID pour inserer une ligne de service
        $arrayMax= $query_model->getLastIDLigneService();
        $nextID = $arrayMax[0]["MAX"];
        $nextID ++;
        $var["ID_LIGNE"] = $nextID;

        return $app['db']->insert("LIGNE_SERVICE", $var);
    }

    function suppLigne(Application $app) {
        $query_model = new QueriesModel($app);

        $ls = $query_model->getLigneServiceByIDLigne($app,$_POST['ID_LIGNE']);

        $FID_GROUPE = $ls[0]['FID_GROUPE'];
        $FID_EC = $ls[0]['FID_EC'];
        $FID_PERS = $ls[0]['FID_PERS'];
        $SEM = $ls[0]['SEM'];
        $ANNEE = $ls[0]['ANNEE'];


        if(!empty($FID_EC) && ($FID_PERS != "404")){

            $countTable = $query_model->countServiceLigne($app,$FID_GROUPE,$FID_EC,$SEM,$ANNEE);
            $count = $countTable[0]['NB'];

            if($count!=0){

                $oldLigne = $query_model->selectServiceLigne($app,$FID_GROUPE,$FID_EC,$SEM,$ANNEE);
                $id =  array('ID_LIGNE'=>$oldLigne[0]['ID_LIGNE']);

                $ls[0]['FID_PERS'] = 404;
                $ls[0]['H_CM'] = $ls[0]['H_CM'] + $oldLigne[0]['H_CM'];
                $ls[0]['H_TD'] = $ls[0]['H_TD'] + $oldLigne[0]['H_TD'];
                $ls[0]['H_EAD'] = $ls[0]['H_EAD'] + $oldLigne[0]['H_EAD'];
                $ls[0]['H_TP'] = $ls[0]['H_TP'] + $oldLigne[0]['H_TP'];
                $var = $ls[0];
                unset($var["ID_LIGNE"]);


                $app['db']->update("LIGNE_SERVICE", $var, $id);

            }else{
                $ls[0]['FID_PERS'] = 404;
                $arrayMax = $query_model->getLastIDLigneService($app);

                $nextID = $arrayMax[0]["MAX"];
                $nextID ++;
                $ls[0]['ID_LIGNE'] = $nextID;
                $var = $ls[0];
                $app['db']->insert("LIGNE_SERVICE",$var);
            }
        }

        return $app['db']->delete("LIGNE_SERVICE",$_POST);
    }

}

?>
