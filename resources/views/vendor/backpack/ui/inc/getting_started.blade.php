<div class="card">
  <div class="card-body">

    <h3>Getting Started</h3>
    <p>If it's your first time using Backpack, we heavily recommend you follow the steps below:</p>

    <div id="accordion" role="tablist">
      <div class="card mb-1">
        <div class="card-header bg-light" id="headingOne" role="tab">
          <h5 class="mb-0 w-100"><a data-bs-toggle="collapse" data-toggle="collapse" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne" class="collapsed"><span class="badge bg-warning me-2">1</span>Create your first CRUD <small class="float-end float-right">1-5 min</small></a></h5>
        </div>
        <div class="collapse" id="collapseOne" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion" style="">
          <div class="card-body">
            <p>You've already got a model, <code class="text-primary bg-light p-1 rounded">App\Models\User</code>... all Laravel projects do. So <strong>let's create a page to administer users</strong>. We want the admin to Create, Read, Update and Delete them. In Backpack, we call that a <a href="https://backpackforlaravel.com/docs/crud-basics?ref=getting-started-widget" target="blank">CRUD</a>. And you can easily generate one for an existing Eloquent model, by running:</p>
            <p>
              <strong><code class="text-primary bg-light p-1 rounded">php artisan backpack:crud user</code></strong>
            </p>
            <p>Run that in your terminal and <strong>choose <code class="text-primary bg-light p-1 rounded">field</code> when asked which <a href="https://backpackforlaravel.com/docs/crud-operation-create#validation?ref=getting-started-widget"
              target="_blank">validation type</a> you'd like</strong>. You can now click on the new sidebar item (or <a href="{{ backpack_url('user') }}">here</a>) and you'll be able to see the entries in the <code class="text-primary bg-light p-1 rounded">users</code> table. Now... even though most generated CRUDs work out-of-the-box, they probably won't be <i>exactly</i> what you need. But that's where Backpack shines, in how easy it is to customize.</p>

            <p>To dig a little deeper, <a href="#" data-bs-toggle="collapse" data-bs-target="#customizeUsersCRUD" data-toggle="collapse" data-target="#customizeUsersCRUD" aria-expanded="true" aria-controls="customizeUsersCRUD">let's make a few changes to the Users CRUD <i class="la la-angle-double-right"></i></a></p>

            <div class="collapse" id="customizeUsersCRUD">
              <p><strong>1. When listing, let's remove <code>setFromDb()</code> and define each column</strong>. To do that, navigate to <code class="text-primary bg-light p-1 rounded">UserCrudController::setupListOperation()</code> and remove the line that says <code class="text-primary bg-light p-1 rounded">setFromDb();</code> - Afterward, manually add the columns for <em>name</em> and <em>email</em>.</p>
                <p>
                  <pre class="language-php rounded"><code class="language-php p-1">
                  protected function setupListOperation()
                  {
                      CRUD::column('name');
                      CRUD::column('email');
                  }
                  </code></pre>
                </p>
              <p><strong>2. On Create & Update, let's add validation to forms</strong>. There are <a href="https://backpackforlaravel.com/docs/crud-operation-create#validation?ref=getting-started-widget" target="_blank">multiple ways to add validation</a> but we've already chosen the simplest, <a href="https://backpackforlaravel.com/docs/crud-operation-create#validating-fields-using-field-attributes?ref=getting-started-widget" target="_blank">validation using field attributes</a>. Let's go to <code class="text-primary bg-light p-1 rounded">setupCreateOperation()</code> and specify our validation rules directly on the fields:
                <p>
                  <pre class="language-php rounded"><code class="language-php p-1">
                  protected function setupCreateOperation()
                  {
                      CRUD::field('name')->validationRules('required|min:5');
                      CRUD::field('email')->validationRules('required|email|unique:users,email');
                      CRUD::field('password')->validationRules('required');
                  }
                  </code></pre>
                </p>
              <p><strong>3. On Create, let's hash the password.</strong> Currently, if we create a new User, it'll work. But if you look in the database... you'll notice the password is stored in plain text. We don't want that - we want it hashed. There are <a href="https://backpackforlaravel.com/docs/crud-operation-create#use-events-in-your-setup-method?ref=getting-started-widget" target="_blank">multiple ways to achieve this too</a>. Let's use Model Events inside <code class="text-primary bg-light p-1 rounded">setupCreateOperation()</code>. Here's how our method could look, when we also tap into the <code class="text-primary bg-light p-1 rounded">creating</code> event, to hash the password:</p>
              <p>
                <pre class="language-php rounded"><code class="language-php p-1">
                  protected function setupCreateOperation()
                  {
                      CRUD::field('name')->validationRules('required|min:5');
                      CRUD::field('email')->validationRules('required|email|unique:users,email');
                      CRUD::field('password')->validationRules('required');

                      // if you are using Laravel 10+ your User model should already include the password hashing in the model casts.
                      // if that's the case, you can skip this step. You can check your model $casts property or `casts()` method.
                      \App\Models\User::creating(function ($entry) {
                          $entry->password = \Hash::make($entry->password);
                      });
                  }
                </code></pre>
              </p>
              <p><strong>4. On Update, let's not require the password</strong>. It should only be needed if an admin wants to change it, right? That means the validation rules will be different for "password". But then again... the rules will also be different for "email" (because on Update, we need to pass the ID to the unique rule in Laravel). Since 2/3 rules are different, let's just delete what was inside <code class="text-primary bg-light p-1 rounded">setupUpdateOperation()</code> and code it from scratch:</p>
              <p>
                <pre class="language-php rounded"><code class="language-php p-1">
                  protected function setupUpdateOperation()
                  {
                      CRUD::field('name')->validationRules('required|min:5');
                      CRUD::field('email')->validationRules('required|email|unique:users,email,'.CRUD::getCurrentEntryId());
                      CRUD::field('password')->hint('Type a password to change it.');

                      // if you are using Laravel 10+ your User model should already include the password hashing in the model casts.
                      // if that's the case, you just need to keep the old password unchanged when the user is updated.
                      \App\Models\User::updating(function ($entry) {
                          if (request('password') == null) {
                            $entry->password = $entry->getOriginal('password');
                          }
                      });

                      // in case you are using an older version of Laravel, or you are not casting your password in the model, you need
                      // to manually hash the password when it's updated by the user
                      \App\Models\User::updating(function ($entry) {
                        if (request('password') == null) {
                            $entry->password = $entry->getOriginal('password');
                        } else {
                            $entry->password = \Hash::make(request('password'));
                        }
                    });
                  }
                </code></pre>
              </p>
              <p>
                That's it. You have a working Users CRUD. Plus, you've already learned some advanced techniques, like <a href="https://backpackforlaravel.com/docs/crud-operation-create#use-events-in-your-setup-method?ref=getting-started-widget" target="_blank">using Model events inside CrudController</a>. Of course, this only scratches the surface of what Backpack can do. To really understand how it works, and how you can best use Backpack's features, <strong>we heavily recommend you move on to the next step, and learn the basics.</strong>
              </p>
            </div>
          </div>
        </div>
      </div>

      <div class="card mb-1">
        <div class="card-header bg-light" id="headingTwo" role="tab">
          <h5 class="mb-0 w-100"><a class="collapsed" data-bs-toggle="collapse" data-toggle="collapse" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo"><span class="badge bg-warning me-2">2</span>Learn the basics <small class="float-end float-right">20-30 min</small></a></h5>
        </div>
        <div class="collapse" id="collapseTwo" role="tabpanel" aria-labelledby="headingTwo" data-parent="#accordion" style="">
          <div class="card-body">
            <p>So you've created your first CRUD? Excellent. Now it's time to understand <i>how it works</i> and <i>what else you can do</i>. Time to learn the basics - how to build and customize admin panels using Backpack. Please follow one of the courses below, depending on how you prefer to learn:</p>
            <ul>
              <li><strong><a target="_blank" href="https://backpackforlaravel.com/docs/getting-started-videos?ref=getting-started-widget">Video Course</a></strong> - 31 minutes</li>
              <li><strong><a target="_blank" href="https://backpackforlaravel.com/docs/getting-started-basics?ref=getting-started-widget">Text Course</a></strong> - 20 minutes</li>
              <li><strong><a target="_blank" href="https://backpackforlaravel.com/getting-started-emails?ref=getting-started-widget">Email Course</a></strong> - 1 email per day, for 4 days, 5 minutes each</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="card mb-1">
        <div class="card-header bg-light" id="headingThree" role="tab">
          <h5 class="mb-0 w-100"><a class="collapsed" data-bs-toggle="collapse" data-toggle="collapse" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree"><span class="badge bg-warning me-2">3</span>Subscribe or purchase <small class="float-end float-right">1-3 min</small></a></h5>
        </div>
        <div class="collapse" id="collapseThree" role="tabpanel" aria-labelledby="headingThree" data-parent="#accordion" style="">
          <div class="card-body">
            <p>If you like Backpack, please <a target="_blank" href="https://github.com/laravel-backpack/crud">give us a star on Gitub</a> - it could help other developer like us find Backpack and join our community.</p>
            <p>If you decide to use Backpack in production, please <strong><a target="_blank" href="https://backpackforlaravel.com/register?ref=getting-started-widget">create a Backpack account</a> and stay subscribed to the Security Newsletter</strong> (1-2 emails per year). That way, we can let you know if your admin panel becomes vulnerable in any way.</p>
            <p>Of course, if you like our free & open-source core, you might also enjoy our premium add-ons:</p>
            <ul>
              <li><strong><a target="_blank" href="https://backpackforlaravel.com/products/pro-for-unlimited-projects?ref=getting-started-widget">PRO</a></strong> - adds 28 fields, 10 filters, 6 columns, 5 operations, 1 widget</li>
              <li><strong><a target="_blank" href="https://backpackforlaravel.com/products/devtools?ref=getting-started-widget">DevTools</a></strong> - easily generate Laravel migrations and models, from a web interface</li>
              <li><strong><a target="_blank" href="https://backpackforlaravel.com/products/figma-template?ref=getting-started-widget">FigmaTemplate</a></strong> - create designs and mockups that are easy to implement in Backpack</li>
              <li><strong><a target="_blank" href="https://backpackforlaravel.com/products/editable-columns?ref=getting-started-widget">EditableColumns</a></strong> - let your admins make quick edits, right from the table view</li>
              <li><strong><a target="_blank" href="https://backpackforlaravel.com/products/calendar-operation?ref=getting-started-widget">CalendarOperation</a></strong> - let your admins see and manage model entries, directly on a calendar</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="card mb-1">
        <div class="card-header bg-light" id="headingThree" role="tab">
          <h5 class="mb-0 w-100"><a class="collapsed" data-bs-toggle="collapse" data-toggle="collapse" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour"><span class="badge bg-warning me-2">4</span>Hide this notice <small class="float-end float-right">1 min</small></a></h5>
        </div>
        <div class="collapse" id="collapseFour" role="tabpanel" aria-labelledby="headingThree" data-parent="#accordion" style="">
          <div class="card-body">Go to your <code class="text-primary bg-light p-1 rounded">config/backpack/ui.php</code> and change <code class="text-primary bg-light p-1 rounded">show_getting_started</code> to <code class="text-primary bg-light p-1 rounded">false</code>.</div>
        </div>
      </div>
    </div>

    <p class="mt-3 mb-0"><small>* this card is only visible on <i>localhost</i>. Follow the last step to hide it from <i>localhost</i> too.</small></p>
  </div>
</div>

@push('after_styles')
  @basset('https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.6.0/styles/base16/dracula.min.css')
@endpush

@push('after_scripts')
  @basset('https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.6.0/highlight.min.js')
  <script>hljs.highlightAll();</script>
@endpush
