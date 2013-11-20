<?php

namespace Xavifuefer\CoffeeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Redirect;
use Symfony\Component\HttpFoundation\JsonResponse;

use Snc\RedisBundle\Command\RedisFlushdbCommand;

use Xavifuefer\CoffeeBundle\Entity\Bean;
use Xavifuefer\CoffeeBundle\Entity\Term;

class DefaultController extends Controller
{
    /**
     * @Template("XavifueferCoffeeBundle:Default:grind.html.twig")
     */
    public function searchAction(Request $request)
    {
        $term = new Term();
        $form = $this->createFormBuilder($term)
            ->add('query', 'search')
            ->getForm();

        $form->handleRequest($request);

        if( $form->isValid() ){
            $em = $this->getDoctrine()->getManager();

            $term->setQuery( $form->getData()->getQuery() );

            $urls = str_replace(array('[',']' ), "", $request->get('urls'));
            $urls = split(',', $urls);
            foreach( $urls as $url ) {
                $bean = new Bean();
                $bean->setUrl( $url );
                $term->addBean( $bean );
            }

            $em->persist( $term );
            $em->flush();

            return new JsonResponse(array(
                'success' => true,
                'url' => $this->generateUrl('xavifuefer_coffee_blender', array(
                    'term' => $term->getId(),
                ), true)
            ));
        }

        return array( 'form' => $form->createView() );
    }

    /**
     * @Template("XavifueferCoffeeBundle:Default:blend.html.twig")
     */
    public function blendAction(Request $request, Term $term = null)
    {
        if( !$term ) { return $this->redirect( $this->generateUrl('xavifuefer_coffee_homepage') ); }

        return array( 'beans' => $term->getBeans(), 'term' => $term );
    }

    public function grindAction(Request $request, Bean $bean)
    {
        if( !$bean ) { return new JsonResponse( array( 'success' => false ) ); }

        $redis = $this->container->get('snc_redis.default');

        $redis->set('bean:' . $bean->getId() . ':' . 'amount', $request->get('bean_amount'));
        $redis->set('bean:' . $bean->getId() . ':' . 'url', $bean->getUrl());

        return new JsonResponse( array(
            'success' => true,
            'bean' => array(
                'amount' => $redis->get('bean:' . $bean->getId() . ':' . 'amount'),
                'url' => $redis->get('bean:' . $bean->getId() . ':' . 'url'),
            )
        ));
    }

    /**
     * @Template("XavifueferCoffeeBundle:Default:thanks.html.twig")
     */
    public function thanksAction(Request $request, Term $term = null)
    {
        if( !$term ) { return $this->redirect( $this->generateUrl('xavifuefer_coffee_homepage') ); }

        // remove created Term from the DB
        $em = $this->getDoctrine()->getManager();
        $em->remove($term);
        $em->flush();

        // Clean REDIS storage
        $redis = $this->container->get('snc_redis.default');
        $redis->flushdb();
    }
}
