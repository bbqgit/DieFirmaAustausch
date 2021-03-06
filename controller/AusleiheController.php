<?php

/**
 * Description of AusleiheController
 *
 * @author Teilnehmer
 */
class AusleiheController implements DoAction {

    public static function doAction($action, $id) {
        switch ($action) {

            case 'showList':
                $out = Ausleihe::getAll();
                $out = self::transform($out);
                break;

            case 'showUpdate':
                $out = Ausleihe::getById($id);
                $out = self::transformUpdate($out);
                break;

            case 'showInsert':
                $out = self::transformUpdate();
                break;

            case 'update' :
                $fahrzeugFiltered = filter_input(INPUT_POST, 'fahrzeug', FILTER_SANITIZE_MAGIC_QUOTES & FILTER_SANITIZE_SPECIAL_CHARS);
                $mitarbeiterFiltered = filter_input(INPUT_POST, 'mitarbeiter', FILTER_SANITIZE_MAGIC_QUOTES & FILTER_SANITIZE_SPECIAL_CHARS);
                $vonFiltered = filter_input(INPUT_POST, 'von', FILTER_SANITIZE_MAGIC_QUOTES & FILTER_SANITIZE_SPECIAL_CHARS);
                $bisFiltered = filter_input(INPUT_POST, 'bis', FILTER_SANITIZE_MAGIC_QUOTES & FILTER_SANITIZE_SPECIAL_CHARS);
                $updateausleiheidFiltered = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT & FILTER_SANITIZE_SPECIAL_CHARS);
                $out = new Ausleihe(Auto::getById($fahrzeugFiltered), Mitarbeiter::getById($mitarbeiterFiltered), HTML::dateAndTimeToDateTime($vonFiltered), HTML::dateAndTimeToDateTime($bisFiltered), $updateausleiheidFiltered);
                $out = Ausleihe::update($out);
                $out = Ausleihe::getAll();
                $out = self::transform($out);
                break;

            case 'insert' :
                $fahrzeugFiltered = filter_input(INPUT_POST, 'fahrzeug', FILTER_SANITIZE_MAGIC_QUOTES & FILTER_SANITIZE_SPECIAL_CHARS);
                $mitarbeiterFiltered = filter_input(INPUT_POST, 'mitarbeiter', FILTER_SANITIZE_MAGIC_QUOTES & FILTER_SANITIZE_SPECIAL_CHARS);
                $vonFiltered = filter_input(INPUT_POST, 'von', FILTER_SANITIZE_MAGIC_QUOTES & FILTER_SANITIZE_SPECIAL_CHARS);
                $bisFiltered = filter_input(INPUT_POST, 'bis', FILTER_SANITIZE_MAGIC_QUOTES & FILTER_SANITIZE_SPECIAL_CHARS);
                $out = new Ausleihe(Auto::getById($fahrzeugFiltered), Mitarbeiter::getById($mitarbeiterFiltered), HTML::dateAndTimeToDateTime($vonFiltered), HTML::dateAndTimeToDateTime($bisFiltered), NULL);
                $out = Ausleihe::insert($out);
                $out = Ausleihe::getAll();
                $out = self::transform($out);
                break;

            case 'delete' :
                $deleteausleiheidFiltered = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT & FILTER_SANITIZE_SPECIAL_CHARS);
                $out = $deleteausleiheidFiltered;
                $out = Ausleihe::delete($out);
                $out = Ausleihe::getAll();
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
        foreach ($out as $ausleihe) {
            $returnOut[$i]['herstellerName'] = $ausleihe->getAuto()->getHersteller()->getName();
            $returnOut[$i]['modell'] = $ausleihe->getAuto()->getName();
            $returnOut[$i]['kennzeichen'] = $ausleihe->getAuto()->getKennzeichen();
            $returnOut[$i]['nachname'] = $ausleihe->getMitarbeiter()->getVorname();
            $returnOut[$i]['vorname'] = $ausleihe->getMitarbeiter()->getNachname();
            $returnOut[$i]['von'] = HTML::dateTimeToDateAndTime($ausleihe->getVon());
            $returnOut[$i]['bis'] = HTML::dateTimeToDateAndTime($ausleihe->getBis());
            $returnOut[$i]['bearbeiten'] = HTML::buildButton('bearbeiten', $ausleihe->getId(), 'bearbeitenAusleihe', 'bearbeiten');
            $returnOut[$i]['loeschen'] = HTML::buildButton('löschen', $ausleihe->getId(), 'loeschenAusleihe', 'loeschen');
            $i++;
        }
        return $returnOut;
    }

    private static function transformUpdate($out = NULL) {
        $returnOut = [];
        $linkeSpalte = [];
        $rechteSpalte = [];

        for ($i = 0; $i < count(Ausleihe::getNames()); $i++) {
            array_push($linkeSpalte, Ausleihe::getNames()[$i]);
        }

        if ($out !== NULL) {
            array_push($linkeSpalte, HTML::buildInput('hidden', 'id', $out->getId()));
        } else {
            array_push($linkeSpalte, '');
        }

        if ($out !== NULL) {
            $dbWerte = json_decode(json_encode($out), true);
        }

        // überführe $dbWerte in rechte Spalte    
        $selected = NULL;
        if ($out !== NULL) {
            if ($out->getAuto() !== NULL) {
                $selected = $out->getAuto()->getId(); // Foreign Key
            }
        }
        $options = Option::buildOptions('Auto', $selected);

        $selected = NULL;
        if ($out !== NULL) {
            if ($out->getMitarbeiter() !== NULL) {
                $selected = $out->getMitarbeiter()->getId(); // Foreign Key
            }
        }
        $options2 = Option::buildOptions('Mitarbeiter', $selected);

        if ($out !== NULL) {
            array_push($rechteSpalte, HTML::buildDropDown('fahrzeug', '1', $options, NULL, 'fahrzeug'));
            array_push($rechteSpalte, HTML::buildDropDown('mitarbeiter', '1', $options2, NULL, 'mitarbeiter'));
            array_push($rechteSpalte, HTML::buildInput('text', 'vonTag', HTML::extractDateFromDateTime($dbWerte['von']), NULL, 'vonTag', NULL, 'TT.MM.JJJJ'));
            array_push($rechteSpalte, HTML::buildInput('text', 'vonZeit', HTML::extractTimeFromDateTime($dbWerte['von'])));
            array_push($rechteSpalte, HTML::buildInput('text', 'bisTag', HTML::extractDateFromDateTime($dbWerte['bis']), NULL, 'bisTag', NULL, 'TT.MM.JJJJ'));
            array_push($rechteSpalte, HTML::buildInput('text', 'bisZeit', HTML::extractTimeFromDateTime($dbWerte['bis'])));
            array_push($rechteSpalte, HTML::buildButton('OK', 'ok', 'updateAusleihe', 'OK'));
        } else {
            array_push($rechteSpalte, HTML::buildDropDown('fahrzeug', '1', $options, NULL, 'fahrzeug'));
            array_push($rechteSpalte, HTML::buildDropDown('mitarbeiter', '1', $options2, NULL, 'mitarbeiter'));
            array_push($rechteSpalte, HTML::buildInput('text', 'vonTag', '', NULL, 'vonTag', NULL, 'TT.MM.JJJJ'));
            array_push($rechteSpalte, HTML::buildInput('text', 'vonZeit', '', NULL, 'vonZeit', NULL, 'HH:MM'));
            array_push($rechteSpalte, HTML::buildInput('text', 'bisTag', '', NULL, 'bisTag', NULL, 'TT.MM.JJJJ'));
            array_push($rechteSpalte, HTML::buildInput('text', 'bisZeit', '', NULL, 'bisZeit', NULL, 'HH:MM'));
            array_push($rechteSpalte, HTML::buildButton('OK', 'ok', 'insertAusleihe', 'OK'));
        }
        $returnOut = HTML::buildFormularTable($linkeSpalte, $rechteSpalte);
        return $returnOut;
    }

}
