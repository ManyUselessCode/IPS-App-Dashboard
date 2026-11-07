<?php

declare(strict_types=1);

/**
 * Startseite der Kachel-Visualisierung im Stil von Home Assistant:
 * eine HTML-Kachel mit den Räumen als Raster. Icon und Name kommen aus
 * dem Raumordner, ein Tipp öffnet den Raum.
 */
class Dashboard extends IPSModule
{
    private const ICON_ERSATZ = 'house';

    public function Create()
    {
        parent::Create();

        $this->RegisterPropertyString('Raeume', '[]');
        $this->SetVisualizationType(1);
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();

        foreach ($this->GetMessageList() as $sender => $nachrichten) {
            foreach ($nachrichten as $nachricht) {
                $this->UnregisterMessage($sender, $nachricht);
            }
        }
        foreach ($this->GetReferenceList() as $referenz) {
            $this->UnregisterReference($referenz);
        }

        foreach ($this->Raeume() as $raum) {
            $this->RegisterReference($raum['RaumID']);
            $this->RegisterMessage($raum['RaumID'], OM_CHANGENAME);
            $this->RegisterMessage($raum['RaumID'], OM_CHANGEICON);
            if ($raum['TemperaturID'] > 0) {
                $this->RegisterReference($raum['TemperaturID']);
                $this->RegisterMessage($raum['TemperaturID'], VM_UPDATE);
            }
        }

        $this->SetStatus(IS_ACTIVE);
        $this->Senden();
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        $this->Senden();
    }

    public function GetVisualizationTile()
    {
        $html = file_get_contents(__DIR__ . '/module.html');
        $daten = json_encode($this->Daten(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
        return str_replace('/*DATEN*/null', $daten, $html);
    }

    private function Senden(): void
    {
        $this->UpdateVisualizationValue(json_encode($this->Daten(), JSON_UNESCAPED_UNICODE));
    }

    private function Raeume(): array
    {
        $liste = json_decode($this->ReadPropertyString('Raeume'), true);
        $raeume = [];
        foreach (is_array($liste) ? $liste : [] as $eintrag) {
            $raumID = (int) ($eintrag['RaumID'] ?? 0);
            if ($raumID <= 0 || !IPS_CategoryExists($raumID)) {
                continue;
            }
            $temperaturID = (int) ($eintrag['TemperaturID'] ?? 0);
            if ($temperaturID > 0 && !IPS_VariableExists($temperaturID)) {
                $temperaturID = 0;
            }
            $raeume[] = ['RaumID' => $raumID, 'TemperaturID' => $temperaturID];
        }
        return $raeume;
    }

    private function Daten(): array
    {
        $daten = [];
        foreach ($this->Raeume() as $raum) {
            $objekt = IPS_GetObject($raum['RaumID']);
            $daten[] = [
                'id'    => $raum['RaumID'],
                'name'  => $objekt['ObjectName'],
                'icon'  => $objekt['ObjectIcon'] != '' ? $objekt['ObjectIcon'] : self::ICON_ERSATZ,
                'info'  => $raum['TemperaturID'] > 0 ? GetValueFormatted($raum['TemperaturID']) : ''
            ];
        }
        return $daten;
    }
}
