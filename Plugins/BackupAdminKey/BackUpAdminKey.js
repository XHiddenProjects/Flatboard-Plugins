document.querySelector('input[name="trip"]').addEventListener("input", function(){
    let x = document.querySelector('input[name="trip"]');
    $.getJSON("./plugin/BackupAdminKey/key/KEY_USER.json", function(d){
        if(x.value === d.hash){
            x.value = d.user;
        }else{
            x.value = x.value;
        }
    });
});