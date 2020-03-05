<?php
namespace App\Validator;

use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;


trait Validation{

  private $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
      $this->entityManager = $entityManager;
  }

  public function if_unique($column,$email,$table)
  {
      $conn = $this->entityManager->getConnection();

      $sql = 'SELECT * FROM '. $table .' WHERE '. $column .' = :email';
      $stmt = $conn->prepare($sql);
      $stmt->execute(['email'=>$email]);

      // returns an array of arrays (i.e. a raw data set)
      //dd($stmt->fetchAll());
      return empty($stmt->fetchAll()) ? true : false;
  }


  public function validate($request,$arr = [])
  {
    $messages = [];

      foreach ($arr as $index=>$value) {
          // required
          $val = explode('|',$value);
          foreach ($val as $val2) {
              if($val2 == 'required' || $val2 == 'array')
              {
                    if($val2 == 'required')
                    {
                        if(!array_key_exists($index,$request))
                        {
                          $messages[$index] = $index . ' is required';
                        }elseif(($request[$index] === null || $request[$index] == '') || $request[$index] == ' ')
                        {
                          $messages[$index] = $index . ' is required';
                        }
                    }elseif($val2 == 'array')
                    {
                        if(!array_key_exists($index,$messages))
                        {
                          if(!array_key_exists($index,$request))
                          {
                              $messages[$index] = $index . ' is required';
                          }elseif(!is_array($request[$index]))
                          {
                              $messages[$index] = $index . ' must me an array';
                          }
                        }
                   }
              }elseif(substr( $val2, 0, 6 ) == "unique"){
                  $val2 = explode(':',$val2);
                  if(!array_key_exists($index,$messages))
                  {
                    //dd($this->if_unique($index,$request[$index],$val2[1]));
                      if(!$this->if_unique($index,$request[$index],$val2[1]))
                      {
                          $messages[$index] = $index . ' is used by someone';
                      }
                  }
              }elseif(substr( $val2, 0, 4 ) == "same"){
                  $val2 = explode(':',$val2);
                  if(!array_key_exists($index,$messages))
                  {
                    //dd($this->if_unique($index,$request[$index],$val2[1]));
                    
                      if($request[$index] != $request[$val2[1]])
                      {
                          $messages[$index] = $index . ' must be same ' . $val2[1];
                      }
                  }
              }
          }
      }
      return $messages;
  }

  public function addFlashArray(string $type,array $message): void
  {
      if (!$this->container->has('session')) {
          throw new \LogicException(' "error".');
      }

      $this->container->get('session')->getFlashBag()->add($type, $message);
  }
}
