function find(){
  var startDate = $("#startDate").val();
  var endDate = $("#endDate").val();
  var checkValue=$("#dataType").val();
  if(checkValue == "0"){
    alert("请选择查询的类型");
    return;
  }
  if(startDate == ""){
    alert("请选择开始时间");
    return;
  }
  if(endDate == ""){
    alert("请选择结束时间");
    return;
  }
  if(checkValue == "1"){
    window.location = "/weekorderbycity1/jump?startDate="+startDate+"&endDate="+endDate;
  }
  if(checkValue == "2"){
    window.location = "/weekcomplaintbranch1/jump?startDate="+startDate+"&endDate="+endDate;
  }
  if(checkValue == "3"){
    window.location = "/complainttag1/jump?startDate="+startDate+"&endDate="+endDate;
  }
  if(checkValue == "4"){
    window.location = "/tagonclick1/jump?startDate="+startDate+"&endDate="+endDate;
  }
  if(checkValue == "5"){
    window.location = "/verificationrate1/jump?startDate="+startDate+"&endDate="+endDate+"&type=sqy";
  }
  if(checkValue == "6"){
    window.location = "/VerificationRate/jump?startDate="+startDate+"&endDate="+endDate+"&type=wxp";
  }
  if(checkValue == "7"){
    window.location = "/salesrate1/jump?startDate="+startDate+"&endDate="+endDate+"&type=wxp";
  }
  if(checkValue == "8"){
    window.location = "/tagsales1/jump?startDate="+startDate+"&endDate="+endDate+"&type=wxp";
  }
  if(checkValue == "9"){
    window.location = "/offLinedetail1/jump?startDate="+startDate+"&endDate="+endDate+"&type=wxp";
  }
}

function excelExport(){
  var startDate = $("#startDate").val();
  var endDate = $("#endDate").val();
  window.location.href="excelExport?startDate="+startDate+"&endDate="+endDate;
}