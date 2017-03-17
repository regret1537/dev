<?php
    // define('ROOT_PATH', '/vhost/train.sunup.net/htdocs/railway');
    // include(ROOT_PATH . '/mysql.inc');

    /**
     * 印出內容
     * @param  string $content 內容
     * @return string
     */
    function display($content)
    {
        echo $content . PHP_EOL;
    }

    /**
     * 格式化身份證
     * @param  string $id 身份證字號
     * @return string
     */
    function formatId($id)
    {
        return str_pad($id, 10);
    }

    /**
     * 解析 AAT 電文至陣列
     * @param  string $type 類別
     * @param  string $aat  電文
     * @return array/boolean
     */
    function parseAat($type, $aat)
    {
        /**
         * AAT 解析欄位
         * @var array
         */
        $aatColumns = [
            '002B' => [
                '002B',
                'result',
                'count',
                'goId',
                'goSn',
                'goDay',
                'goTime',
                'goTrain',
                'goType',
                'goStartStop',
                'goDeliverStop',
                'goTicketCount',
                'goAdult',
                'goChild',
                'goOld',
                'goDisable',
                'goDirection',
                'bkId',
                'bkSn',
                'bkDay',
                'bkTime',
                'bkTrain',
                'bkType',
                'bkStartStop',
                'bkDeliverStop',
                'bkTicketCount',
                'bkAdult',
                'bkChild',
                'bkOld',
                'bkDisable',
                'bkDirection',
                'tradeCount',
                'ticketDirection01',
                'ticketType01',
                'ticketAmount01',
                'ticketCount01',
                'ticketTotal01',
                'ticketDirection02',
                'ticketType02',
                'ticketAmount02',
                'ticketCount02',
                'ticketTotal02',
                'ticketDirection03',
                'ticketType03',
                'ticketAmount03',
                'ticketCount03',
                'ticketTotal03',
                'ticketDirection04',
                'ticketType04',
                'ticketAmount04',
                'ticketCount04',
                'ticketTotal04',
                'ticketDirection05',
                'ticketType05',
                'ticketAmount05',
                'ticketCount05',
                'ticketTotal05',
                'ticketDirection06',
                'ticketType06',
                'ticketAmount06',
                'ticketCount06',
                'ticketTotal06',
                'ticketDirection07',
                'ticketType07',
                'ticketAmount07',
                'ticketCount07',
                'ticketTotal07',
                'ticketDirection08',
                'ticketType08',
                'ticketAmount08',
                'ticketCount08',
                'ticketTotal08',
                'feeTotal',
                'carType',
                'trainType',
                'hasBento',
                'goBentoCount',
                'goBentoAmount',
                'goVegetableBentoCount',
                'goVegetableBentoAmount',
                'bkBentoCount',
                'bkBentoAmount',
                'bkVegetableBentoCount',
                'bkVegetableBentoAmount'
            ],
            '005B' => [
                '005B',
                'result',
                'necRrn',
                'goId',
                'goSn',
                'bkId',
                'bkSn',
                'fee',
                'responseTime'
            ],
        ];
        if (substr($aat, -1) === '#') {
            $result = explode('*', substr($aat, 0, -1));
            return array_combine($aatColumns[$type], $result);
        } else {
            return false;
        }
    }

    /**
     * 送 NEC
     * @param  string $ip      IP
     * @param  string $port    Port
     * @param  string $content 內容
     * @param  string $timeout 逾時
     * @return string/boolean
     */
    function send($ip, $port, $content, $timeout)
    {
        $length = strlen($content);
        $fp = fsockopen($ip, $port, $errno, $errstr, $timeout);
        if ($fp === false) {
            return false;
        }

        $writeBytes = fwrite($fp, $content, $length);
        if ($writeBytes === false) {
            return false;
        }

        $response = fread($fp, $length);
        if ($response === false) {
            return false;
        }
        fclose($fp);

        return $response;
    }

    /**
     * 產生驗證碼
     * @param  array $data 資料
     * @return string
     */
    function generateAuthCode($data)
    {
        // 金額絕對值
        $absoluteAmount = abs($data['amount']);

        $snPiece = substr($data['gwPaySn'], -4);
        $code = ($snPiece * $absoluteAmount) % 3;
        return $code;
    }

    /**
     * 產生關帳電文
     * @param  array $data 關帳資料
     * @return string
     */
    function generateCloseStatement($url, $data)
    {
        $close = [
            's' => $data['gwPaySn'],
            'a' => abs($data['amount']), // 金額絕對值
            't' => $data['type'],
            'c' => generateAuthCode($data), // 驗證碼
            'Ntime' => (isset($data['responseTime']) ? $data['responseTime'] : date('YmdHis'))
        ];

        $statement = '[' . $url . '/tarin_close.php?' . http_build_query($close) . ']';
        return $statement;
    }

    /**
     * 取得手續費
     * @param  string $return  NEC 回傳手續費
     * @param  string $default 預設手續費
     * @return string
     */
    function getFee($return, $default)
    {
        $cleanReturn = trim($return);
        if (empty($cleanReturn)) {
            return $default;
        } else {
            return $return;
        }
    }

    /**
     * 取得回應時間
     * @param  string $return  NEC 回傳回應時間
     * @param  string $default 預設回應時間
     * @return string
     */
    function getResponseTime($return, $default)
    {
        if (empty($return)) {
            return $default;
        } else {
            return $return;
        }
    }


    try {
        $originalTable = 'uorder_gwp'; // 原始資料表
        $reserveTable = 'uorder_bk'; // 訂票完成資料表
        $dealTable = 'uorder'; // 成交資料表
        $odnb = '541393514'; // 訂單編號
        $tmstp = strtotime('2017/01/17 01:00:00'); // 交易時間(以原始交易時間為主)
        $rTime =  date('Y/m/d H:i', $tmstp); // 交易時間
        $aatIp = $sockUrl; // AAT IP
        $aatPort = $sockPORT; // AAT Port
        $aatTimeout = $iAAT_Timeout; // AAT Port
        $amount = '-366'; // 退款金額
        $fee = '40'; // 手續費(以 005B 為主)
        $responseTime = date('YmdHis'); // 回應時間(以 005B 為主)
        $rClose = '0'; // 請款關帳狀態
        $rCancel = '1'; // 退款關帳狀態
        $statement005A = '005A*541393514*S223802797*985122*          *      *00406*0000*2017/01/17 01:00#'; // 005A 電文
        $payUrl = $GWECpauUrl; // 關帳 URL
        $type = 'minus'; // 關帳類別
        $apiIp = $sockUrl; // API IP
        $apiPort = $sockPORTclose; // API Port
        $apiTimeout = 20; // API Timeout

        // 訂單編號檢查
        if (trim($odnb) === '') {
            throw new Exception('1');
        }

        // 連接資料庫
        $myLink = mylink();
        if ($myLink === 0) {
            throw new Exception('2');
        }

        // 檢查是否退過款
        $sql = 'SELECT rcancel FROM ' . $dealTable;
        $sql .= ' WHERE rcancel=1 AND odnb="' . $odnb . '"';
        $sql .= ' LIMIT 1';
        $query = myquery($sql, $myLink);
        if ($query === false) {
            throw new Exception('3');
        }
        $rowNum = mysql_num_rows($query);
        if ($rowNum > 0) {
            throw new Exception('4');
        }

        // 取得 002B 電文
        $sql = 'SELECT odnb,pack2,card8,guolu,gwpaysn,glwhere,pack3,uppp,eci,RailwayType FROM ' . $dealTable;
        $sql .= ' WHERE rclose!=0 AND odnb="' . $odnb . '"';
        $sql .= ' LIMIT 1';
        $query = myquery($sql, $myLink);
        if ($query === false) {
            throw new Exception('5');
        }
        $result = mysql_fetch_array($query);
        if ($result === false) {
            throw new Exception('6');
        }

        $odnb = $result['odnb']; // 訂單編號
        $pack2 = $result['pack2'] . '#'; // 002B 電文
        $card8 = $result['card8']; // 卡號前 6 後 4
        $guolu = $result['guolu']; // 國旅
        $gwPaySn = $result['gwpaysn']; // 授權單號
        $glWhere = $result['glwhere']; // 國旅起點
        $pack3 = $result['pack3'] . '#'; // 003B 電文
        $uppp = $result['uppp']; // 訂票張數
        $eci = $result['eci']; // ECI
        $railwayType = $result['RailwayType']; // 火車類別

        // 解析 002B 電文
        $data002B = parseAat('002B', $pack2);

        $goId = $data002B['goId']; // 去程 ID
        $goSn = $data002B['goSn']; // 去程電腦代碼
        $bkId = $data002B['bkId']; // 回程 ID
        $bkSn = $data002B['bkSn']; // 回程電腦代碼

        // 取得 005B 電文
        $statement005B = send($aatIp, $aatPort, $statement005A, $aatTimeout);
        if ($statement005B === false) {
            throw new Exception('6');
        }
        display('005B:' . $statement005B);

        // 解析 005B 電文(不檢查回應結果)
        $data005B = parseAat('005B', $pack2);

        $necRrn = $data005B['necRrn']; // NEC 回傳交易號碼
        $sPay = getFee($data005B['fee'], $fee); // 退票手續費
        $bsPay = getFee($data005B['fee'], $fee); // 退票手續費
        $responseTime = getResponseTime($data005B['responseTime'], $responseTime); // 交易回應時間

        $pack3With005B = $pack3 . '*' . $statement005B; // 加入 005B 電文

        // 寫入備份退款資料
        $sql = 'INSERT INTO ' . $reserveTable . ' SET';
        $fields = [
            'amount',
            'odnb',
            'tmstp',
            'goSn',
            'bkSn',
            'rTime',
            'card8',
            'guolu',
            'gwPaySn',
            'necRrn',
            'glWhere',
            'pack2',
            'uppp',
            'sPay',
            'bsPay',
            'eci',
            'rClose',
            'rCancel',
        ];
        foreach ($fields as $field) {
            $sql .= ' ' . strtolower($field) . ' = "' . ${$field} . '",';
        }
        $sql .= ' goid = AES_ENCRYPT("' . $goId . '","' . $key_str_ch . '"),';
        $sql .= ' bkid = AES_ENCRYPT("' . $bkId . '","' . $key_str_ch . '"),';
        $sql .= ' pack3 = "' . $pack3With005B . '",';
        $sql .= ' RailwayType = "' . $railwayType . '"';
        if (myquery($sql, $myLink) === false) {
            display('sql: ' . $sql);
        }

        // 產生檢查碼
        $authCode = generateAuthCode($gwPaySn, $amount);

        // 金額不等於 0 才退刷 (多餘判斷?)
        if ($amount) {
            $closeStatement = generateCloseStatement($payUrl, compact(['gwPaySn', 'amount', 'type', 'responseTime']));
            display('close: ' . $closeStatement);
            $response = send($apiIp, $apiPort, $closeStatement, $apiTimeout);
            display('response: ' . $response);
            // 不檢查關帳結果
            // if(substr($response, 0, 4) != 'inok') {
                // throw new Exception('9');
            // }
        } else {
            throw new Exception('8');
        }

        // 寫入退款資料
        $sql = 'INSERT INTO ' . $dealTable . ' SET';
        foreach ($fields as $field) {
            $sql .= ' ' . strtolower($field) . ' = "' . ${$field} . '",';
        }
        $sql .= ' goid = AES_ENCRYPT("' . $goId . '","' . $key_str_ch . '"),';
        $sql .= ' bkid = AES_ENCRYPT("' . $bkId . '","' . $key_str_ch . '"),';
        $sql .= ' pack3 = "' . $pack3With005B . '",';
        $sql .= ' RailwayType = "' . $railwayType . '"';
        if (myquery($sql, $myLink) === false) {
            display('sql: ' . $sql);
        }
        display('finish');
    } catch (Exception $e) {
        display('Error code: ' . $e->getMessage());
    }