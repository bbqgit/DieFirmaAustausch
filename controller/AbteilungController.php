<?php

/**
 * Description of AbteilungController
 *
 * @author Teilnehmer
 */
class AbteilungController {

    public static function doAction($action, &$view, $id) {
        switch ($action) {
            case 'showList':

                $out = Abteilung::getAll();
                $out = self::transform($out);
                break;

            case 'showUpdate':
                $out = Abteilung::getById($id);
                $out = self::transformUpdate($out);
                break;

            case 'showInsert':
                $out = self::transformUpdate();
                break;
            case 'insert':
//                echo '<pre>';
//                print_r($_POST);
//                echo '</pre>';
                $out = new Abteilung($_POST['abteilung'], '');
                $out = Abteilung::insert($out);
                $out = Abteilung::getAll();
                $out = self::transform($out);
                break;
            case 'update':
                $out = new Abteilung($_POST['abteilung'], $_POST['id']);
                $out = Abteilung::update($out);
                $out = Abteilung::getAll();
                $out = self::transform($out);
                break;
            case 'delete':
                echo '<pre>';
                print_r($_POST);
                echo '</pre>';
                $out = new Abteilung('',$_POST['id']);
                $out = Abteilung::delete($out);
                $out = Abteilung::getAll();
                $out = self::transform($out);
                break;
            default:
                break;
        }
        return $out;
    }

    private static function transform($out) {
        $returnOut;
        $i = 0;
        foreach ($out as $abteilung) {
            $returnOut[$i]['abteilungName'] = $abteilung->getName();
            $returnOut[$i]['bearbeiten'] = HTML::buildButton('bearbeiten', $abteilung->getId(), 'editAbteilung', 'bearbeiten');
            $returnOut[$i]['loeschen'] = HTML::buildButton('löschen', $abteilung->getId(), 'editAbteilung', 'löschen');
            $i++;
        }
        return $returnOut;
    }

    private static function transformUpdate($out = NULL) {

        $returnOut = [];
        $linkeSpalte = [];
        $rechteSpalte = [];

        for ($i = 0; $i < count(Abteilung::getNames()); $i++) {
            array_push($linkeSpalte, Abteilung::getNames()[$i]);
        }
        if ($out == NULL) {
            array_push($linkeSpalte, HTML::buildInput('hidden', 'id', ''));
            $rechteSpalte[0] = HTML::buildInput('text', 'name', '', NULL, 'name');
            array_push($rechteSpalte, HTML::buildButton('OK', 'ok', 'insertAbteilung', 'OK'));
            $returnOut = HTML::buildFormularTable($linkeSpalte, $rechteSpalte);
            return $returnOut;
        } else {
            $options = [];
            array_push($linkeSpalte, HTML::buildInput('hidden', 'id', $out->getId(), NULL, 'id'));
            $dbWerte = json_decode(json_encode($out), true);
            // überführe $dbWerte in rechte Spalte
            array_push($rechteSpalte, HTML::buildInput('text', 'name', $dbWerte['name'], NULL, 'name'));
            array_push($rechteSpalte, HTML::buildButton('OK', 'ok', 'updateAbteilung', 'OK'));
            $returnOut = HTML::buildFormularTable($linkeSpalte, $rechteSpalte);
            return $returnOut;
        }
    }

}
