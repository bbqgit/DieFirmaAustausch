<?php

class Mitarbeiter implements Aenderbar, JsonSerializable {

    private $id;
    private $vorname;
    private $nachname;
    private $geschlecht;
    private $geburtsdatum;
    private $abteilung;
    private $stundenlohn;
    private $vorgesetzter;

    public static function getNames() {
        return ['Vorname', 'Nachname', 'Geschlecht', 'Geburtsdatum', 'Abteilung', 'Stundenlohn', 'Vorgesetzter'];
    }

    function __construct($vorname, $nachname, $geschlecht, $geburtsdatum, Abteilung $abteilung, $stundenlohn, Mitarbeiter $vorgesetzter = NULL, $id = NULL) {
        $this->id = $id;
        $this->vorname = $vorname;
        $this->nachname = $nachname;
        $this->geschlecht = $geschlecht;
        $this->geburtsdatum = $geburtsdatum;
        $this->abteilung = $abteilung;
        $this->stundenlohn = $stundenlohn;
        $this->vorgesetzter = $vorgesetzter;
    }

    public static function delete($id) {
        
    }

    public static function getById($id) {
        $pdo = DbConnect::connect();
        $sql = "SELECT * from mitarbeiter WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return new Mitarbeiter($rows[0]['vorname'], $rows[0]['nachname'], $rows[0]['geschlecht'], $rows[0]['geburtsdatum'], Abteilung::getById($rows[0]['abteilung_id']), $rows[0]['stundenlohn'], Mitarbeiter::getVorgesetzterById($rows[0]['vorgesetzter_id']), $rows[0]['id']);
    }

    public static function getVorgesetzterById($id) {
        if ($id !== NULL) {
            $pdo = DbConnect::connect();
            $sql = "SELECT * from mitarbeiter WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return new Mitarbeiter($rows[0]['vorname'], $rows[0]['nachname'], $rows[0]['geschlecht'], $rows[0]['geburtsdatum'], Abteilung::getById($rows[0]['abteilung_id']), $rows[0]['stundenlohn'], NULL, $rows[0]['id']);
        } else {
            return NULL;
        }
    }

    public static function insert($object) {
        
    }

    public static function update($object) {
        
    }

    public static function getAll() {
        $pdo = DbConnect::connect();
        $sql = "SELECT * from mitarbeiter";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $mit = [];

        foreach ($rows as $row) {
            $mit[$row['id']] = new Mitarbeiter($row['vorname'], $row['nachname'], $row['geschlecht'], $row['geburtsdatum'], Abteilung::getById($row['abteilung_id']), $row['stundenlohn'], Mitarbeiter::getVorgesetzterById($row['vorgesetzter_id']), $row['id']);
        }
        return $mit;
    }

    function getId() {
        return $this->id;
    }

    function getVorname() {
        return $this->vorname;
    }

    function getNachname() {
        return $this->nachname;
    }

    function getGeschlecht() {
        return $this->geschlecht;
    }

    function getGeburtsdatum() {
        return $this->geburtsdatum;
    }

    function getAbteilung() {
        return $this->abteilung;
    }

    function getStundenlohn() {
        return $this->stundenlohn;
    }

    function getVorgesetzter() {
        return $this->vorgesetzter;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setVorname($vorname) {
        $this->vorname = $vorname;
    }

    function setNachname($nachname) {
        $this->nachname = $nachname;
    }

    function setGeschlecht($geschlecht) {
        $this->geschlecht = $geschlecht;
    }

    function setGeburtsdatum($geburtsdatum) {
        $this->geburtsdatum = $geburtsdatum;
    }

    function setAbteilung(Abteilung $abteilung) {
        $this->abteilung = $abteilung;
    }

    function setStundenlohn($stundenlohn) {
        $this->stundenlohn = $stundenlohn;
    }

    function setVorgesetzter(Mitarbeiter $vorgesetzter) {
        $this->vorgesetzter = $vorgesetzter;
    }

    public function jsonSerialize() {
        return['id' => $this->id,
            'vorname' => $this->vorname,
            'nachname' => $this->nachname,
            'geschlecht' => $this->geschlecht,
            'geburtsdatum' => $this->geburtsdatum,
            'abteilung' => $this->abteilung,
            'stundenlohn' => $this->stundenlohn,
            'vorgesetzter' => $this->vorgesetzter];
    }

}

?>