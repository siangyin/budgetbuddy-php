 <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
            <div class="field">
              <p class="control has-icons-left has-icons-right">
                <input class="input" type="email" name="email" placeholder="Email">
                <span class="icon is-small is-left">
                  <i class="fas fa-envelope"></i>
                </span>
              </p>
            </div>
            <div class="field">
              <p class="control has-icons-left">
                <input class="input" type="password" name="password" placeholder="Password">
                <span class="icon is-small is-left">
                  <i class="fas fa-lock"></i>
                </span>
              </p>
            </div>
            <div class="field">
              <p class="control">
                <input type="submit" class="button is-danger" value="Login"/>
              </p>
            </div>
          </form>