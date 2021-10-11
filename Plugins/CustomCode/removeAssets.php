<?php
if(!unlink("./assets/custom.css")){
    echo "Error: can not remove file";
}
else if(!unlink("./assets/custom.js")){
    echo "Error: can not remove file";
}else{
    echo "<script>window.history.back(-1);</script>";
}

?>