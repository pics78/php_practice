$(function() {
  $('.item-val input:disabled').parent().addClass('--no-edit');
  $('.item-val select:disabled').parent().addClass('--no-edit');
  $('.item-val textarea:disabled').parent().addClass('--no-edit');
  $('.form-area textarea:enabled').prev('p').addClass('--enabled');

  let $clicked = null;
  let $formArea = null;
  $('.form-area a').on('click', function() {
    $clicked = $(this);
    $formArea = $clicked.parent('.form-area');
    const no = $('#emp-no').text();
    const year = $('#target-year').val();
    const $textForm = $formArea.find('textarea');
    const type = $textForm.attr('name');
    const content = $textForm.val();
    let data = {
      'emp_no': no,
      'year': year,
      'eval-type': type,
      'eval-content': content
    };

    const $evaluator = $formArea.find('p.evaluator');
    const evaluatorNo = $('#evaluator-no').val();
    if ($evaluator.length && evaluatorNo) {
      data['evaluator-no'] = evaluatorNo;
    }
    $.ajax({
      type: 'POST',
      url: '../actions/load.php',
      data: { 'eval': data },
      dataType: 'json'
    })
    .done(function(res) {
      if (res.result === 'success') {
        $clicked.addClass('--updated');
        setTimeout(function() {
          $clicked.removeClass('--updated');
        }, 5000);

        if (res.evaluator) {
          $evaluator.removeClass('hide');
          $evaluator.text(res.evaluator);
        }
      }
    })
    .fail(function(e) {
      console.log(e);
    });
  });

  $('#year-selector select').change(function() {
    const year = $(this).val();
    const no = $('#emp-no').text();
    window.location.href = 'evaluation.php?no=' + no + '&y=' + year;
  });

  // モーダル処理
  $('.js-modal-open').on('click',function(){
    $('.js-modal').fadeIn();
    return false;
  });
  $('.js-modal-close').on('click',function(){
    $('.js-modal').fadeOut();
    return false;
  });

});
