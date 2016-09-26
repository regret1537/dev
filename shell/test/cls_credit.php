<?php
    class cls_credit {
        // 信用卡別清單
        private $aCard_Type_List = array(
            'M' => 'MASTER',
            'V' => 'VISA',
            'J' => 'JCB'
        );
        
        // 卡號 2 開頭的 Master BIN 碼範圍
        private $iMaster_Start_Range = 222100;
        private $iMaster_End_Range = 272099;
        
        // UCAF 對應表
        private $aMaster_UCAF_List = array(
            '5' => '2',
            '6' => '1',
            '7' => '0',
        );
        
        private $sCard_No = ''; // 信用卡號
        private $sCard_Type = ''; // 信用卡別
        private $sUCAF = ''; // UCAF(Master 卡有 ECI 才有)
        private $sECI = ''; // ECI
        
        public function __construct($sCard_No, $sECI) {
            $this->sCard_No = $sCard_No; // 設定卡號
            $this->sECI = $sECI; // 設定 ECI
            $this->set_card_type(); // 設定卡別
        }
        
        // 設定卡別
        private function set_card_type() {
            $sFirst_Chr = substr($this->sCard_No, 0, 1);
            $sTmp_Card_Type = '';
            switch ($sFirst_Chr) {
                case '5':
                    // Master
                    $sTmp_Card_Type = $this->aCard_Type_List['M'];
                    break;
                case '4':
                    // Visa
                    $sTmp_Card_Type = $this->aCard_Type_List['V'];
                    break;
                case '3':
                    // JCB
                    $sTmp_Card_Type = $this->aCard_Type_List['J'];
                    break;
                case '2':
                    // 部份為 Master
                    $s5_Card_No = substr($this->sCard_No, 0, 6);
                    $i5_Card_No = intval($s5_Card_No);
                    if ($i5_Card_No >= $this->iMaster_Start_Range and $i5_Card_No <= $this->iMaster_End_Range) {
                        $sTmp_Card_Type = $this->aCard_Type_List['M'];
                    }
                    break;
                default:
            }
            $this->sCard_Type = $sTmp_Card_Type;
        }
        
        // 取得 UCAF
        public function get_ucaf() {
            $sTmp_UCAF = '';
            // Master 有 ECI 表示有 3D 驗證
            if ($this->sCard_Type == $this->aCard_Type_List['M']) {
                $sTmp_UCAF = $this->aMaster_UCAF_List[$this->sECI];
            }
            return $sTmp_UCAF;
        }
                
    }
?>