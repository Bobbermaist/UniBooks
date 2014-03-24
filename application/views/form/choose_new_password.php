
<?php echo form_open("reset/password/$id/$confirm_code"); ?>
        
      <h1>Scegli una nuova password</h1>
      <p> <?php echo form_password($new_password); ?> </p>

<?php
        echo form_submit('reset_pass', 'Conferma');
echo form_close(); 
?>

 
