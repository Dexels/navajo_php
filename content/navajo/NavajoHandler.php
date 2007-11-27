<?php

NavajoClient :: updateNavajoFromPost();

# based on the label of the submit, get correct id
if (isset ($_REQUEST['submit'])) {
    $submit = $_REQUEST['submit'];
    if (isset ($_REQUEST[$submit . ':target'])) {
        $target = $_REQUEST[$submit . ':target'];
    }
    if (isset($_REQUEST[$submit . ':serverCall'])) {
        $_REQUEST['serverCall'] = $_REQUEST[$submit . ':serverCall'];
    }
}

# call webservice if the formSecret session variable matches the POST variable (to prevent duplicates)

if (isset ($_REQUEST['serverCall']) && isset($_SESSION['formId'])) {
    if (strcasecmp($_POST['form_id'], $_SESSION['formId']) === 0) {
        $actions = explode(';', $_REQUEST['serverCall']);
        foreach ($actions as $current) {
            $initscr = explode(':', $current);
            try {
                if (count($initscr) == 2) {
                    $nnn = NavajoClient :: callService($initscr[0], $initscr[1]);
                } else {
                    $nnn = NavajoClient :: callInitService($initscr[0]);
                }
            } catch (Exception $e) {
                $_REQUEST['errormessage'] = $e->getMessage();
                echo $e->getMessage();
            }
        }
        unset($_SESSION['formId']);
    }
}

if (!isset ($_REQUEST['action'])) {
    $_REQUEST['action'] = $defaultPage;
}
/*
if (isset($target) || isset ($_REQUEST['action'])) {
    if ($_REQUEST['action'] == 'exit') {
        if (isset ($_SESSION['site'])) {
            $_SESSION['site']->onDestroySession();
        }
        session_destroy();
        unset ($_REQUEST['action']);
        if (!isset ($_SESSION['site'])) {
            $_SESSION['site'] = new WebSite();
            $_SESSION['site']->onStartSession();
        }
        include $siteHome . $defaultPage . '.php';
    } else {
        $_SESSION['currentPage'] = (isset($target))?$target:$_REQUEST['action'];
        include $_SERVER['DOCUMENT_ROOT'] . $siteHome . $_SESSION['currentPage'] . '.php';
    }
}
*/
# set a new form secret for the next form
if(!isset($_SESSION['formId'])) {
    $secret = md5(uniqid(rand(), true));
    $_SESSION['formId'] = $secret;
}

?>
<script language="JavaScript">
$(document).ready(function() {
  
  $('form :input').blur(function() {
    var $listItem = $(this).parents('li:first');
    var $warnItem = $listItem.children('label');

    $warnItem.removeClass('error');
    $listItem.removeClass('warning');

    if ($(this).is('.required')) {
      var $listItem = $(this).parents('li:first');
      var $warnItem = $listItem.children('label');
      if (this.value == '') {
        $warnItem.addClass('error');
        $listItem.addClass('warning');
      };
    };

    if ($(this).is('.email')) {
      var $listItem = $(this).parents('li:first');
      var $warnItem = $listItem.children('label');
      if (this.value != '' && !/.+@.+\.[a-zA-Z]{2,4}$/.test(this.value)) {
        $warnItem.addClass('error');
        $listItem.addClass('warning');
      };
    };

    if ($(this).is('.date')) {
      var $listItem = $(this).parents('li:first');
      var $warnItem = $listItem.children('label');
      if (this.value != '' && !/[1-2]{1}[0-9]{3}-[0-1]{1}[0-9]{1}-[0-3]{1}[0-9]{1}$/.test(this.value)) {
        $warnItem.addClass('error');
        $listItem.addClass('warning');
      };
    };
  });

  $('form').submit(function() {
    $(':input.required').trigger('blur');
    var numWarnings = $('.warning', this).length;
    if (numWarnings) {
      return false;
    };
  });

  $(function() {
      $('.date').datePicker();
  });

  $('input.update').each(function() {
      $(this).before('<span class="update">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
      var $saveDisk = $(this).siblings('span.update');
      $saveDisk.click(function(){
          $(this).siblings('input.update').attr('checked', 'true');
          $updateBtn = $(this).parents('table').siblings('input.updateBtn');
          $updateBtn.click();
      });
  });

  $('input.delete').each(function() {
      $(this).before('<span class="delete">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
      var $deleteDisk = $(this).siblings('span.delete');
      $deleteDisk.click(function(){
          $(this).siblings('input.delete').attr('checked', 'true');
          $deleteBtn = $(this).parents('table').siblings('input.deleteBtn');
          $deleteBtn.click();
      });
  });
});
</script>
