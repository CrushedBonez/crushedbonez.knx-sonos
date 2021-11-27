<?php
// Backup implemented:
// "{8A4D3B17-F8D7-4905-877F-9E69CEC3D579}"
class KNXSonosDevice extends IPSModule
{
    public function __construct($InstanceID)
    {
        //Never delete this line!
        parent::__construct($InstanceID);
    }

    public function Destroy()
    {
        //Never delete this line!
        parent::Destroy();
    }

    public function Create()
    {
        //Never delete this line!
        parent::Create();

        $this->ConnectParent('{1C902193-B044-43B8-9433-419F09C641B8}');
        // Properties to store Sonos instance and KNX group addresses
        $this->RegisterPropertyInteger('SonosInstance', 0);
        $this->RegisterPropertyInteger('GA_Play_Pause1', 0);
        $this->RegisterPropertyInteger('GA_Play_Pause2', 0);
        $this->RegisterPropertyInteger('GA_Play_Pause3', 0);
        $this->RegisterPropertyInteger('GA_Status_Play_Pause1', 0);
        $this->RegisterPropertyInteger('GA_Status_Play_Pause2', 0);
        $this->RegisterPropertyInteger('GA_Status_Play_Pause3', 0);
        $this->RegisterPropertyInteger('GA_Stop1', 0);
        $this->RegisterPropertyInteger('GA_Stop2', 0);
        $this->RegisterPropertyInteger('GA_Stop3', 0);
        $this->RegisterPropertyInteger('GA_Status_Stop1', 0);
        $this->RegisterPropertyInteger('GA_Status_Stop2', 0);
        $this->RegisterPropertyInteger('GA_Status_Stop3', 0);
        $this->RegisterPropertyInteger('GA_Volume1', 0);
        $this->RegisterPropertyInteger('GA_Volume2', 0);
        $this->RegisterPropertyInteger('GA_Volume3', 0);
        $this->RegisterPropertyInteger('GA_Status_Volume1', 0);
        $this->RegisterPropertyInteger('GA_Status_Volume2', 0);
        $this->RegisterPropertyInteger('GA_Status_Volume3', 0);
        $this->RegisterPropertyInteger('GA_Prev_Next1', 0);
        $this->RegisterPropertyInteger('GA_Prev_Next2', 0);
        $this->RegisterPropertyInteger('GA_Prev_Next3', 0);
        $this->RegisterPropertyInteger('GA_Favorites1', 0);
        $this->RegisterPropertyInteger('GA_Favorites2', 0);
        $this->RegisterPropertyInteger('GA_Favorites3', 0);
        $this->RegisterPropertyInteger('GA_Artist1', 0);
        $this->RegisterPropertyInteger('GA_Artist2', 0);
        $this->RegisterPropertyInteger('GA_Artist3', 0);
        $this->RegisterPropertyInteger('GA_Title1', 0);
        $this->RegisterPropertyInteger('GA_Title2', 0);
        $this->RegisterPropertyInteger('GA_Title3', 0);
        // Attributes to store variable IDs
        $this->RegisterAttributeInteger('VarSonosVolume', 0);
        $this->RegisterAttributeInteger('VarSonosStatus', 0);
        $this->RegisterAttributeInteger('VarSonosArtist', 0);
        $this->RegisterAttributeInteger('VarSonosTitle', 0);
        $this->RegisterAttributeInteger('VarKnxPlayPause', 0);
        $this->RegisterAttributeInteger('VarKnxStatusPlayPause', 0);
        $this->RegisterAttributeInteger('VarKnxStop', 0);
        $this->RegisterAttributeInteger('VarKnxStatusStop', 0);
        $this->RegisterAttributeInteger('VarKnxVolume', 0);
        $this->RegisterAttributeInteger('VarKnxStatusVolume', 0);
        $this->RegisterAttributeInteger('VarKnxPrevNext', 0);
        $this->RegisterAttributeInteger('VarKnxFavorites', 0);
        $this->RegisterAttributeInteger('VarKnxArtist', 0);
        $this->RegisterAttributeInteger('VarKnxTitle', 0);
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
        # Data-Array
        # $data[0] => Value
        # $data[1] => Changed
        # $data[2] => Old Value
        # $data[3] => Timestamp
        # $data[4] => Old Timestamp
        $SonosInstance =  $this->ReadPropertyInteger('SonosInstance');
        $VarSonosVolume = $this->ReadAttributeInteger('VarSonosVolume');
        $VarSonosStatus = $this->ReadAttributeInteger('VarSonosStatus');
        $VarSonosArtist = $this->ReadAttributeInteger('VarSonosArtist');
        $VarSonosTitle = $this->ReadAttributeInteger('VarSonosTitle');
        $VarKnxPlayPause = $this->ReadAttributeInteger('VarKnxPlayPause');
        //$GaPlayPause = @IPS_GetParent($VarKnxPlayPause);
        $VarKnxStatusPlayPause = $this->ReadAttributeInteger('VarKnxStatusPlayPause');
        $GaStatusPlayPause = @IPS_GetParent($VarKnxStatusPlayPause);
        $VarKnxStop = $this->ReadAttributeInteger('VarKnxStop');
        //$GAStop = @IPS_GetParent($VarKnxStop);
        $VarKnxStatusStop = $this->ReadAttributeInteger('VarKnxStatusStop');
        $GaStatusStop = @IPS_GetParent($VarKnxStatusStop);
        $VarKnxVolume = $this->ReadAttributeInteger('VarKnxVolume');
        //$GaVolume = @IPS_GetParent($VarKnxVolume);
        $VarKnxStatusVolume = $this->ReadAttributeInteger('VarKnxStatusVolume');
        $GaStatusVolume = @IPS_GetParent($VarKnxStatusVolume);
        $VarKnxPrevNext = $this->ReadAttributeInteger('VarKnxPrevNext');
        //$GaPrevNext = @IPS_GetParent($VarKnxPrevNext);
        $VarKnxFavorites = $this->ReadAttributeInteger('VarKnxFavorites');
        //$GaFavorites = @IPS_GetParent($VarKnxFavorites);
        $VarKnxArtist = $this->ReadAttributeInteger('VarKnxArtist');
        $GaArtist = @IPS_GetParent($VarKnxArtist);
        $VarKnxTitle = $this->ReadAttributeInteger('VarKnxTitle');
        $GaTitle = @IPS_GetParent($VarKnxTitle);
        
        // Handle updates from the Sonos instance
        if ($Data[1] && ($SenderID === $VarSonosVolume)) {
            // volume changed on Sonos, Update KNX status
            $this->SendDebug("MessageSink", "Changed Volume from " . $Data[2] . " to " . $Data[0], 0);
            KNX_WriteDPT5($GaStatusVolume, $Data[0]);
        } elseif ($Data[1] && ($SenderID === $VarSonosStatus)) {
            // Sonos state changed, update KNX Play/Pause and Stop status
            $Status = array("Zurück", "Wiedergabe", "Pause", "Stop", "Vor", "Übergang");
            $this->SendDebug("MessageSink", "Changed Status from \"" . $Status[$Data[2]] . "\" to \"" . $Status[$Data[0]] . "\"", 0);
            if (($Data[0] === 2) || ($Data[0] === 3)) {
                // Pause or Stop
                KNX_WriteDPT1($GaPlayPause, 0);
                KNX_WriteDPT1($GaStatusPlayPause, 0);
                KNX_WriteDPT1($GaStatusStop, 1);
            } elseif ($Data[0] === 1) {
                // Play
                KNX_WriteDPT1($GaPlayPause, 1);
                KNX_WriteDPT1($GaStatusPlayPause, 1);
                KNX_WriteDPT1($GaStatusStop, 0);
            }
        } elseif ($Data[1] && ($SenderID === $VarSonosArtist)) {
            // Sonos artist changed, update KNX artist status
            $this->SendDebug("MessageSink", "Changed Artist from \"" . utf8_decode($Data[2]) . "\" to \"" . utf8_decode($Data[0]) . "\"", 0);
            KNX_WriteDPT16($GaArtist, utf8_decode($Data[0]));
        } elseif ($Data[1] && ($SenderID === $VarSonosTitle)) {
            // Sonos title changed, update KNX title status
            $this->SendDebug("MessageSink", "Changed Title from \"" . utf8_decode($Data[2]) . "\" to \"" . utf8_decode($Data[0]) . "\"", 0);
            KNX_WriteDPT16($GaTitle, utf8_decode($Data[0]));
        }
        
        // Handle updates from the KNX instances
        elseif ($SenderID === $VarKnxPlayPause) {
            // Play/Pause changed via KNX
            if ($Data[0] === true) {
                $this->SendDebug("Change Play/Pause", "start playing", 0);
                SNS_Play($SonosInstance);
            } else {
                $this->SendDebug("Change Play/Pause", "pause", 0);
                SNS_Pause($SonosInstance);
            }
        } elseif ($SenderID === $VarKnxStop) {
            $this->SendDebug("Stop Playback", "stop playback", 0);
            SNS_Stop($SonosInstance);
        } elseif ($SenderID === $VarKnxVolume) {
            if ($Data[0] === true) {
                $this->SendDebug("Change Volume", "Decrease volume by 2%", 0);
                SNS_ChangeVolume($SonosInstance, -2);
            } else {
                $this->SendDebug("Change Volume", "Increase volume by 2%", 0);
                SNS_ChangeVolume($SonosInstance, 2);
            }
        } elseif ($SenderID === $VarKnxPrevNext) {
            if ($Data[0] === true) {
                $this->SendDebug("Previous/Next Title", "Go to previous title", 0);
                SNS_Previous($SonosInstance);
            } else {
                $this->SendDebug("Previous/Next Title", "Go to next title", 0);
                SNS_Next($SonosInstance);
            }
        }
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();
        $Childs = array("GA_Play_Pause", "GA_Status_Play_Pause", "GA_Stop", "GA_Status_Stop", "GA_Volume", "GA_Status_Volume", "GA_Prev_Next", "GA_Favorites", "GA_Artist", "GA_Title");
        $AttributeMap = array (
            'GA_Play_Pause' => 'VarKnxPlayPause',
            'GA_Status_Play_Pause' => 'VarKnxStatusPlayPause',
            'GA_Stop' => 'VarKnxStop',
            'GA_Status_Stop' => 'VarKnxStatusStop',
            'GA_Volume' => 'VarKnxVolume',
            'GA_Status_Volume' => 'VarKnxStatusVolume',
            'GA_Prev_Next' => 'VarKnxPrevNext',
            'GA_Favorites' => 'VarKnxFavorites',
            'GA_Artist' => 'VarKnxArtist',
            'GA_Title' => 'VarKnxTitle'
        );
        $ModuleID_DPT001 = "{F3058AB2-AFDC-4479-A053-5F4599DF6F5B}";
        $ModuleID_DPT005 = "{EBD0EE8D-DFA5-449F-BBE4-49CFC5F0EEB4}";
        $ModuleID_DPT016 = "{2146FF76-FAE6-46B6-9630-73E5F3FAE190}";
        $MessageList = $this->GetMessageList();
        
        foreach ($MessageList as $Sender => $MessageArray) {
            foreach ($MessageArray as $Message) {
                $this->SendDebug("MessageSink", "Unregister message $Message from sender $Sender", 0);
                $this->UnregisterMessage($Sender, $Message);
            }
        }

        # subscribe to Sonos instance
        $SonosInstance = $this->ReadPropertyInteger('SonosInstance');
        if ($SonosInstance > 0) {
            $ObjectIDStatus = @IPS_GetObjectIDByIdent("Status", $SonosInstance);
            $ObjectIDVolume = @IPS_GetObjectIDByIdent("Volume", $SonosInstance);
            $ObjectIDArtist = @IPS_GetObjectIDByIdent("Artist", $SonosInstance);
            $ObjectIDTitle = @IPS_GetObjectIDByIdent("Title", $SonosInstance);
            if ($ObjectIDStatus > 0) {
                $this->WriteAttributeInteger('VarSonosStatus', $ObjectIDStatus);
                $this->RegisterMessage($ObjectIDStatus, VM_UPDATE);
            } else {
                $this->WriteAttributeInteger('VarSonosStatus', 0);
            }
            if ($ObjectIDVolume > 0) {
                $this->WriteAttributeInteger('VarSonosVolume', $ObjectIDVolume);
                $this->RegisterMessage($ObjectIDVolume, VM_UPDATE);
            } else {
                $this->WriteAttributeInteger('VarSonosVolume', 0);
            }
            if ($ObjectIDArtist > 0) {
                $this->WriteAttributeInteger('VarSonosArtist', $ObjectIDArtist);
                $this->RegisterMessage($ObjectIDArtist, VM_UPDATE);
            } else {
                $this->WriteAttributeInteger('VarSonosArtist', 0);
            }
            if ($ObjectIDTitle > 0) {
                $this->WriteAttributeInteger('VarSonosTitle', $ObjectIDTitle);
                $this->RegisterMessage($ObjectIDTitle, VM_UPDATE);
            } else {
                $this->WriteAttributeInteger('VarSonosTitle', 0);
            }
        }

        # create and subscribe to KNX instances
        foreach ($Childs as $Child) {
            $Address1 = $this->ReadPropertyInteger($Child . "1");
            $Address2 = $this->ReadPropertyInteger($Child . "2");
            $Address3 = $this->ReadPropertyInteger($Child . "3");
            if (($Address=1 === 0) && ($Address2 === 0) && ($Address3 === 0)) {
                # do nothing for empty GA 0/0/0
            } else {
                if (($Child === "GA_Artist") || ($Child === "GA_Title")) {
                    $GAType = $ModuleID_DPT016;
                } elseif ($Child === "GA_Status_Volume") {
                    $GAType = $ModuleID_DPT005;
                } else {
                    $GAType = $ModuleID_DPT001;
                }
                $ChildInstance = @IPS_GetObjectIDByIdent($Child, $this->InstanceID);
                if ($ChildInstance === false) {
                    $ChildInstance = IPS_CreateInstance($GAType);
                    IPS_SetName($ChildInstance, $Child);
                    IPS_SetIdent($ChildInstance, $Child);
                    IPS_SetParent($ChildInstance, $this->InstanceID);
                    IPS_SetProperty($ChildInstance, "Address1", $Address1);
                    IPS_SetProperty($ChildInstance, "Address2", $Address2);
                    IPS_SetProperty($ChildInstance, "Address3", $Address3);
                    if (($Child === "GA_Status_Volume") || ($Child === "GA_Stop") || ($Child === "GA_Status_Stop") || ($Child === "GA_Artist") || ($Child === "GA_Title"))  {
                        IPS_SetProperty($ChildInstance, "Dimension", 1);
                    } elseif (($Child === "GA_Play_Pause") || ($Child === "GA_Status_Play_Pause"))  {
                        IPS_SetProperty($ChildInstance, "Dimension", 10);
                    } elseif (($Child === "GA_Volume") || ($Child === "GA_Prev_Next"))  {
                        IPS_SetProperty($ChildInstance, "Dimension", 8);
                    }
                    IPS_ApplyChanges($ChildInstance);
                } else {
                    IPS_SetProperty($ChildInstance, "Address1", $Address1);
                    IPS_SetProperty($ChildInstance, "Address2", $Address2);
                    IPS_SetProperty($ChildInstance, "Address3", $Address3);
                    IPS_ApplyChanges($ChildInstance);
                }
                $VariableID = IPS_GetObjectIDByIdent("Value", $ChildInstance);
                if ($VariableID > 0) {
                    $this->WriteAttributeInteger($AttributeMap[$Child], $VariableID);
                    $this->RegisterMessage($VariableID, VM_UPDATE);
                } else {
                    $this->WriteAttributeInteger($AttributeMap[$Child], 0);
                }
            }
        }
    }
}
