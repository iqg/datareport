function find(){
  var startDate = $("#startDate").val();
  var endDate = $("#endDate").val();
  var checkValue=$("#dataType").val();
  var city = $("#city").val();
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
    window.location = "/weekorderbycity/jump?startDate="+startDate+"&endDate="+endDate;
  }
  if(checkValue == "2"){
    window.location = "/weekcomplaintbranch/jump?startDate="+startDate+"&endDate="+endDate;
  }
  if(checkValue == "3"){
    window.location = "/complainttag/jump?startDate="+startDate+"&endDate="+endDate;
  }
  if(checkValue == "4"){
    window.location = "/tagonclick/jump?startDate="+startDate+"&endDate="+endDate;
  }
  if(checkValue == "5"){
    window.location = "/verificationrate/jump?startDate="+startDate+"&endDate="+endDate+"&type=sqy";
  }
  if(checkValue == "6"){
    window.location = "/verificationrate/jump?startDate="+startDate+"&endDate="+endDate+"&type=wxp";
  }
  if(checkValue == "7"){
    window.location = "/salesrate/jump?startDate="+startDate+"&endDate="+endDate+"&type=wxp";
  }
  if(checkValue == "8"){
    window.location = "/tagsales/jump?startDate="+startDate+"&endDate="+endDate+"&type=wxp";
  }
  if(checkValue == "9"){
    window.location = "/offLinedetail/jump?startDate="+startDate+"&endDate="+endDate+"&type=wxp";
  }
  if(checkValue == "10"){
    window.location = "/cbSaleRate/jump?startDate="+startDate+"&endDate="+endDate+"&city="+city;
  }
}

function excelExport(){
  var startDate = $("#startDate").val();
  var endDate = $("#endDate").val();
  window.location.href="excelExport?startDate="+startDate+"&endDate="+endDate;
}