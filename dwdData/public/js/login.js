$(function(){

  //submit_resigter

  var oldPass, checkOptionsMsg = {}, checkOptionsText = {};
  var dataPage;


  var verifyFunc = function($group, type){
    var result,
        $dom = $group.find('.verify');

    if(!$dom.length) return true;

    var verifyType = $dom.data('type'),
        value = $.trim($dom.val()),
        $tipbox = $group.find('.help-inline');

    if(!value){
      //不能为空
      $group.addClass('error');
      result = false;
      $tipbox.text('不能为空');
      return result;
    }

    var verifyPass = function(){
      result = true;
      $tipbox.text('');
      $group.removeClass('error');
    };
    var verifyNotPass = function(tip){
      result = false;
      $group.addClass('error');
      $tipbox.text(tip);
    };
    if(type == 'keyup'){
      verifyPass();
      return;
    }

    switch(verifyType){
      case 'phone':
        if(checkOptionsText.phone && checkOptionsText.phone != value){
          checkOptionsMsg.phone = '';
        }
        if(!/^1[3|4|5|7|8][0-9]\d{4,8}$/.test(value)){
          verifyNotPass('手机号码格式不正确');
        }else{
          if(checkOptionsMsg.phone){
            verifyNotPass(checkOptionsMsg.phone);
          }else{
            verifyPass();
          }
        }
        break;
      case 'imgverify':
        if(!/^.{5}$/.test(value)){
          verifyNotPass('验证码位数不正确');
        }else{
          verifyPass();
        }
        break;
      case 'phoneverify':
        if(!/^.{6}$/.test(value)){
          verifyNotPass('校验码位数不正确');
        }else{
          verifyPass();
        }
        break;
      case 'password':
        if(dataPage == 'register'){
          if( !/^.{8,20}$/.test(value) ){
            verifyNotPass('密码长度不符合要求');
            return false;
          }else{
            oldPass = value;
            verifyPass();
          }
        }else{
          verifyPass();
          result = true;
        }
        break;
      case 'verifypwd':
        if( !/^.{8,20}$/.test(value) ){
          verifyNotPass('密码长度不符合要求');
        }else{
          if(oldPass && oldPass != value){
            verifyNotPass('两次输入密码不一致');
          }else{
            verifyPass();
          }
        }
        break;
    }
    return result;
  };
  var verifyForm = function($form){
    var verifyTemp = [],
        verifyResult = true;
    var verifyList = $form.find('.control-group');
    verifyList.each(function(index, item){
      var itemPass = verifyFunc($(item));
      verifyTemp.push(!!itemPass);
    });
    //任意一个验证不通过，即验证不通过
    for(var i=0,len = verifyTemp.length;i<len;i++){
      if(!verifyTemp[i]){
        verifyResult = false;
      }
    }
    return verifyResult;
  };

  // 监听输入
  var $form_hsq_x = $('.form_hsq_x');
  $form_hsq_x.delegate('.verify', 'keyup', function(e){
    var $group = $(this).parents('.control-group');
    verifyFunc($group, 'keyup');
  });
  $form_hsq_x.delegate('.verify', 'blur', function(e){
    var $group = $(this).parents('.control-group');
    verifyFunc($group, 'blur');
  });

  //图片验证码点击更新
  $form_hsq_x.delegate('img.img_verify', 'click', function(e){
    var imgSrc = '/util/genimage?' + Math.random();
    $(this).attr('src', imgSrc);
  });

  //发送短信总次数
  var sendPhoneTime, sendPhoneNum = 3;
  $form_hsq_x.delegate('.btn-phone-verify:not(:disabled)', 'click', function(e){
    var inputPhone = '#input_phone';
    var $group = $(inputPhone).parents('.control-group');
    var result = verifyFunc($group);
    if( false == result ) {
      return false;
    }

    $(this).attr('disabled', 'disabled');
    if(--sendPhoneNum < 0){
      $(this).attr('disabled', 'disabled');
      return;
    }
    getPhoneCode({
      $dom: $(this),
      type: 'doing',
      tip: '发送中...'
    });
  });

  function getPhoneCode(opt){
    var $dom = opt.$dom;
    opt.$dom.html(opt.tip);
    var time = 60;
    sendPhoneTime = setInterval(function() {
          if (--time == 0) {
            // Q = false;
            $dom.html('免费获取短信校验码<font color="#999">（今天剩余' + sendPhoneNum + "次 ）</font>");
            $dom.removeAttr("disabled");
            clearInterval(sendPhoneTime);
          } else {
            $dom.html(time + "秒后可重新获取校验码");
          }
        }
        , 1000)
  }

  //注册页面
  $('.submit_resigter').click(function(e){
    e.preventDefault(); //阻止默认行为
    var $form = $(this).parents('form');

    dataPage = $form.data('page');
    var isVerify = verifyForm($form);
    if(isVerify){

      //如果需要选择协议，单独写在这里
      // var $checkbox = $('.input_checkbox');
      // if(!$checkbox[0].checked){
      //   verifyResult = false;
      //   //写一些什么提醒
      // }
      var mobile = $('input[name="mobile"]').val();
      var captchaCode = $('input[name="captchaCode"]').val();
      var phoneCode = $('input[name="phoneCode"]').val();
      var password = $('input[name="password"]').val();
      $.ajax({
        url: "/user/submitregister",
        type: "POST",
        async: false,
        data: {
          mobile: mobile,
          captchaCode: captchaCode,
          phoneCode: phoneCode,
          password: password
        }
      }).done(function (response) {
        w = $.parseJSON( response );
        if( w.errno != 0 ) {
          var verifyId = '';
          verifyId   = '#input_phone';
          if( w.errno == 50102 ) {
            verifyId = '#input_phone_verify';
          }
          if( w.errno == 90011 ) {
            verifyId = '#input_img_verify';
          }
          var $group = $(verifyId).parents('.control-group');
          $group.addClass('error');
          $group.find('.help-inline').text(w.errmsg);
          return false;
        }
        alert('注册成功！');
        location.href = '/user/user';
      });

      //$(this).submit();
    }
  });

  //登录页面
  $('.submit_login').click(function(e){
    e.preventDefault(); //阻止默认行为
    var $form = $(this).parents('form');
    dataPage = $form.data('page');
    var isVerify = verifyForm($form);
    if(isVerify){
      $(this).parents('form').submit();
    }
  });

  //找回密码页面
  $('.submit_resetting').click(function(e){
    e.preventDefault(); //阻止默认行为
    var $form = $(this).parents('form');
    dataPage = $form.data('page');
    var isVerify = verifyForm($form);
    if(isVerify){

      var mobile = $('input[name="mobile"]').val();
      var captchaCode = $('input[name="captchaCode"]').val();
      var phoneCode = $('input[name="phoneCode"]').val();

      $.ajax({
        url: "/user/submitresetting",
        type: "POST",
        async: false,
        data: {
          mobile: mobile,
          captchaCode: captchaCode,
          phoneCode: phoneCode
        }
      }).done(function (response) {
        w = $.parseJSON( response );
        if( w.errno != 0 ) {
          var verifyId = '';
          verifyId   = '#input_phone';
          if( w.errno == 50102 ) {
            verifyId = '#input_phone_verify';
          }
          if( w.errno == 90011 ) {
            verifyId = '#input_img_verify';
          }
          var $group = $(verifyId).parents('.control-group');
          $group.addClass('error');
          $group.find('.help-inline').text(w.errmsg);
          return false;
        }
        location.href = '/user/resetting/reset?mobile=' + w.data.mobile + '&token=' + w.data.token;
      });
    }
  });

  //重置密码页面
  $('.submit_resetting_reset').click(function(e){
    e.preventDefault(); //阻止默认行为
    var $form = $(this).parents('form');
    dataPage = $form.data('page');
    var isVerify = verifyForm($form);
    if(isVerify){
      $(this).parents('form').submit();
    }
  });

  //短信验证
  $('.btn-phone-verify').click(function(){
    var inputPhone = '#input_phone';
    var mobile = $(inputPhone).val();
    var $group = $(inputPhone).parents('.control-group');
    var type = $('input#type-id').val();
    var result = verifyFunc($group);
    $.ajax({
      url: "/util/verifycode",
      type: "GET",
      async: false,
      data: {
        mobile: mobile,
        type: type
      }
    }).done(function(response) {
      var w = $.parseJSON( response );
      if( w.errno != 0 ) {
        checkOptionsMsg.phone = w.errmsg;
        checkOptionsText.phone = mobile;
        verifyFunc($group, 'phone');
        return false;
      }else{
        checkOptionsMsg.phone = '';
      }
      return true;
    }).fail(function() {
      alert('系统错误，获取失败！');
      return false;
    });
  });
});
