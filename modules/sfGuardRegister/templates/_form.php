<?php use_helper('I18N') ?>

<form action="<?php echo url_for('@sf_guard_register') ?>" method="post" id="sf_guard_register_form">
  <table>
    <?php echo $form ?>
    <tfoot>
      <tr>
        <td colspan="2">
          <input type="submit" name="register" value="<?php echo __('Register', null, 'sf_guard') ?>" id="sf_guard_register_form_submit" />
        </td>
      </tr>
    </tfoot>
  </table>
</form>