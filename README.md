# symfony-bolly-validator
validation for symfony framework like laravel

after download trait in src/Validator/Validation.php

in Controller

use Validation;

and will be something like that in our controller method ( ex:- store function )

$errors = $this->validate($request->request->all(),[
            'name'             => 'required',
            'email'            => 'required|unique:admin',
            'password'         => 'required',
            'confirm_password' => 'required|same:password'
        ]);

        if(!empty($errors))
        {
          $this->addFlashArray('errors',$errors);
          // return $this->redirectToRoute('admin.admins.create',['errors' => $errors]);
          return $this->redirectToRoute('admin.admins.create');
        }
        
        
        
in create.html.twig file

                {% if(app.session.flashbag.has('errors')) %}
                    <div class="alert alert-danger">
                        {% for error in app.flashes('errors') %}
                            {% for arr in error %}
                                <li>{{ arr }}</li>
                            {% endfor %}
                        {% endfor %}
                    </div>
                {% endif %}
                
                
