<?php

declare(strict_types=1);

namespace App\Controller;



use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Doctrine\ORM\EntityManagerInterface;

use App\Configuration\DependantEntityConfig;


class DependantEntityController extends AbstractController
{
    #[Route('/admin/dependant_entity/option', name: 'app_dependant_entity_options')]
    public function dependantEntity(
        EntityManagerInterface $em,
        Request $request,
        TranslatorInterface $translator): Response {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $config_name = $request->get('config_name');
        $parent_id    = $request->get('parent_id');
        $placeholder  = $request->get('placeholder');
        $orig_value   = $request->get('origvalue');

        $options = DependantEntityConfig::form_options($config_name);

        #if ($options['role'] !== 'IS_AUTHENTICATED_ANONYMOUSLY') {
        #    $checker = $this->get('security.authorization_checker');
        #    if (false === $checker->isGranted($options['role'])) {
        #        throw new AccessDeniedException();
        #    }
        #}

        $order_field = 'id';
        $order_dir = 'ASC';
        $search_callback = null;
        if(array_key_exists('search_order_field', $options))
            $order_field = $options['search_order_field'];
        if(array_key_exists('search_order_direction', $options))
            $order_dir = $options['search_order_direction'];
        if(array_key_exists('search_callback', $options))
            $search_callback = $options['search_callback'];

        $qb = $em->getRepository($options['class'])->createQueryBuilder('e');

        if (null !== $search_callback) {
            $repository = $qb->getEntityManager()->getRepository($options['class']);

            if (!method_exists($repository, $search_callback)) {
                throw new \InvalidArgumentException(sprintf('Callback function "%s" in Repository "%s" does not exist.', $options['callback'], get_class($repository)));
            }

            # custom filtering, callback must do all filtering
            $qb = $repository->$search_callback($parent_id, $orig_value);

        } else {
            $qb->where('e.' . $options['parent_entity_field'] . ' = :parent_id')
            ->orderBy('e.' . $order_field, $order_dir)
            ->setParameter('parent_id', $parent_id);

            if(null !== $orig_value) {
                $qb->orWhere($qb->expr()->andX(
                    'e.' . $options['parent_entity_field'] . ' = :parent_id',
                    'e = :field_value'))
                ->setParameter(':field_value', $orig_value)
                ;
            }
        }

        $results = $qb->getQuery()->getResult();

        if (empty($results)) {
            return new Response('<option value="">' . $translator->trans($options['no_result_msg']) . '</option>');
        }

        $html = '';
        if ($placeholder !== false)
            $html .= '<option value="">' . $translator->trans($placeholder) . '</option>';

        $getter =  PropertyAccess::createPropertyAccessor();
        #$choice_label = $options['choice_label'];
        $choice_label = null;

        foreach($results as $result)
        {
            if ($choice_label)
                $res = $getter->getValue($result, $choice_label);
            else $res = (string)$result;

            $html = $html . sprintf("<option value=\"%d\">%s</option>",$result->getId(), $res);
        }

        return new Response($html);
    }
}
