<?php

//
//namespace ContactBoxBundle\Form\Type;
//
//use ContactBoxBundle\Model\PersonSearch;
//use Symfony\Component\Form\AbstractType;
//use Symfony\Component\Form\FormBuilderInterface;
//use Symfony\Component\OptionsResolver\OptionsResolverInterface;
//
//class PersonSearchType extends AbstractType
//{
//    public function buildForm(FormBuilderInterface $builder, array $options)
//    {
//        $builder
//            ->add('firstName', null, array(
//                'required' => true, 'trim' => true
//            ))
//            ->add('search', 'submit');
//    }
//
//    public function setDefaultOptions(OptionsResolverInterface $resolver)
//    {
//        parent::setDefaultOptions($resolver);
//        $resolver->setDefaults(array(
//            // avoid to pass the csrf token in the url (but it's not protected anymore)
//            'csrf_protection' => false,
//            'data_class' => 'Obtao\BlogBundle\Model\ArticleSearch'
//        ));
//    }
//
//    public function getName()
//    {
//        return 'article_search_type';
//    }
//}