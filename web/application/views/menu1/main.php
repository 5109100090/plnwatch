<h2><?php echo $pageTitle ?></h2>
<hr />
<?php
echo form_open();
echo 'Area : ' . form_dropdown('area', $dropdownData['area'])
 . ' Rentang Meter-an : ' . form_dropdown('meter', $dropdownData['meter'])
 . ' Rentang Tgl. Pasang : ' . form_dropdown('tglPasang', $dropdownData['tglPasang'])
 . ' ' . form_submit('', ' lihat data ');
echo form_close();
?>