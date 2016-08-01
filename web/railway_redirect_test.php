<?php
    include('../tpl/header.php');
    include('../lib/html_common.inc');
    include('../lib/misc.inc');
    
?>
    <form id="goPayForm" action="https://ticket.chinatrust.com.tw/railway/index.php" method="post" target="_blank">
        <input type="hidden" name="howgo" value=2 />      <!-- 1:單程; 2:去回 -->
        <input type="hidden" name="na" value="" />
        <input type="hidden" name="id1" value=A128157987 />      <!-- 單程票身分證字號 -->
        <input type="hidden" name="go1sn" value=999335 />      <!-- 單程票訂票代碼 -->
        <input type="hidden" name="id2" value=A128157987 />      <!-- 去回票去程身分證字號 -->
        <input type="hidden" name="go2sn" value=999336 />      <!-- 去回票去程訂票代碼 -->
        <input type="hidden" name="id3" value="" />      <!-- 去回票回程身分證字號 -->
        <input type="hidden" name="go3sn" value="" />      <!-- 去回票回程訂票代碼 -->
        <input type="submit" value="網路付款" target="_blank" />
    </form>
<?php   
    include('../tpl/footer.php');
?>