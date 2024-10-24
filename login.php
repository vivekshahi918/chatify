 <?php include_once "header.php"?>
<style>
body{
    background-color: lightblue;
}
</style>
<body>
<div class="container-fluid">
    <div class="row" style="margin-top:130px;">
           <div class="col-sm-4"></div>
            <div class="col-sm-4">
              <div class="card">
                  <div class="card-header"><h3>Chatify</h3></div>
                   <div class="card-body">
                     <form method="post" action="check.php">
                             <div class="row" style="margin-top:15px;">
                             <div class="col-sm-12">
                        <?php
                           if(isset($_GET["empty"])){
                               ?>
                               <div class="alert alert-danger">All Fields Are Required</div>
                               <?php
                            }
                            else if(isset($_GET["invalid_password"])){
                                ?>
                                <div class="alert alert-warning">Password Mismatch</div>
                                <?php
                             } 
                             else if(isset($_GET["invalid_record"])){
                                ?>
                                <div class="alert alert-info">Please Create Account</div>
                                <?php
                             } 
                        ?>
                          </div>
                                 <label>Email-Id</label>
                                 <input type="email" name="email" placeholder="Email-Id" class="form-control">
                             </div>
                             <div class="row" style="margin-top:15px;">
                                 <label>Password</label>
                                 <input type="password" name="pass" placeholder="Password" class="form-control">
                             </div>
                              
                             <div class="row" style="margin-top:15px;">
                                  <button class="btn btn-success form-control" type="submit">continue to chat</button>
                             </div>
                     </form>
                     <div class="row" style="margin-top:25px;">
                         <!-- Example for login.php -->
<h5>New User?</h5><a href="index.php?signup=true"><strong>Signup Now</strong></a>

                     </div>
                 </div>
             </div>
            </div>
            
    </div>
</div>  
</body>
</html>



