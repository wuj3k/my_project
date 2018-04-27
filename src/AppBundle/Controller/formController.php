<?php
/**
 * Created by PhpStorm.
 * User: Kasia
 * Date: 2018-04-25
 * Time: 14:29
 */

namespace AppBundle\Controller;

use Symfony\Component\Intl\Intl;
use AppBundle\Entity\Form;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Locale\Locale;
use Symfony\Component\Routing\Annotation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\NotBlank;


class formController extends Controller
{
    /**
     * @Annotation\Route("/", name="home")
     */
   public function formAction(Request $request)
   {
      // Locale::setDefault('pl');
       $db = new Form();
       $form = $this->createFormBuilder($db)
           ->add('name', TextType::class , array(
               'attr' => array ('placeholder' => 'Podaj imię'),
               'label' => 'Imie',
               'required' => false,
              //'constraints' => new NotBlank(),

           ))
           ->add('surname', TextType::class , array(
               'attr' => array ('placeholder' => 'Podaj nazwisko'),
               'label' => 'Nazwisko',
               'required' => false,
               //'constraints' => new NotBlank(),
           ))
           ->add('experience', RangeType::class , array(
               'attr' => array (
                   "data-provide" => "slider",
                   "data-slider-ticks" => "[1, 2, 3, 4,5,6,7,8,9,10]",
                   "data-slider-ticks-labels" => '["do roku", "2", "3", "4","5","6","7","8","9","+10"]',
                   "data-slider-min" => "0",
                   "data-slider-max" => "10",
                   "data-slider-step" => "1",
                   "data-slider-value" => "1",
                   "data-slider-tooltip" => "hide",
                   "style" => "width:100%;"

                   ),
               'label' => 'Twoje doświadczenie w latach'
           ))
           ->add('city', TextType::class , array(
               'attr' => array ( 'placeholder' => 'Podaj miasto w którym mieszkasz'),
               'label' => 'Miasto',
               'required' => false,
               //'constraints' => new NotBlank(),
           ))
           ->add('country' , CountryType::class , array(
               'attr' => array ( 'placeholder' => 'Podaj kraj w którym mieszkasz'),
               'label' => 'Kraj',
               'preferred_choices' => array('PL'),

           ))
           ->add('time', ChoiceType::class , array(
              // 'attr' => array ('class' => 'custom-control custom-checkbox mb-3 mr-3'),
               'label' => 'Dyspozyjność',
               'expanded' => 'true',
               'multiple' => 'true',
               'choices' => array('praca zdalna' => 'Praca zdalna','praca na miejscu' => 'praca na miejscu'),


           ))
           ->add('profession', ChoiceType::class , array(

               'label' => 'Profesja',
               'choices' => array (
                   'Programista' => 'Programista',
                   'Koder' => 'Koder',
                   'Designer' => 'Designer'
               )
               ))
           ->add('save',SubmitType::class, array(
               'attr' => array ('class' => 'btn btn-success'),
               'label' => 'Zapisz'))
           ->getForm();

       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {

           $country = Intl::getRegionBundle()->getCountryName($form['country']->getData());

           $db->setName(ucwords(mb_strtolower($form['name']->getData())));
           $db->setSurname(ucwords(mb_strtolower($form['surname']->getData())));
           $db->setCity(ucwords(mb_strtolower($form['city']->getData())));
           $db->setExperience($form['experience']->getData());
           $db->setTime(implode(",",$form['time']->getData()));
           $db->setCountry($country);
           $db->setProfession($form['profession']->getData());


           $entityManager = $this->getDoctrine()->getManager();
           $entityManager->persist($db);
          $entityManager->flush();



          return $this->redirectToRoute('show');

       }

       return $this->render('form/form.html.twig', array(
           'form' => $form->createView()));
    }

    /**
     * @Annotation\Route("/show" , name="show")
     */
    public function showAction(){

        $users = $this->getDoctrine()
            ->getRepository(Form::class)
            ->findAll();

       return $this->render('form/show.html.twig', array( 'users' => $users));
    }
}