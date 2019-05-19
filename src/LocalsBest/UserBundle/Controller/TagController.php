<?php

namespace LocalsBest\UserBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use LocalsBest\CommonBundle\Entity\Tag;
use LocalsBest\UserBundle\Entity\Tag as Tag2;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TagController extends Controller
{
    /**
     * Add Tag to object
     *
     * @param Request $request
     * @param int $id
     * @param string $type
     *
     * @return JsonResponse
     */
    public function addTagAction(Request $request, $id, $type)
    {
        $em = $this->getDoctrine()->getManager();

        // Get Object Entity
        $object = $this->findOr404($type, array('id' => $id), 'Invalid item');
        // Get tag from request
        $tag = $request->request->get('tag');

        // check method exist for Object Entity
        if (!method_exists($object, 'addTag')) {
            // Return fail JSON
            return new JsonResponse(
                ['result' => 'fail']
            );
        }
        // Get first Business of current user
        $business = $this->getUser()->getBusinesses()->first();
        // Special Tags Rule for Kelly Business
        if($business->getId() == 155 && $this->getUser() != $business->getOwner()) {
            $managerTag = $em->getRepository('LocalsBestCommonBundle:Tag')
                ->findOneBy(['tag' => $tag, 'objectType' => $type, 'createdBy' => $business->getOwner()]);

            if($managerTag === null) {
                // Return fail JSON
                return new JsonResponse(
                    ['result' => 'fail']
                );
            }
        }
        // Disable Soft Delete Filter
        $em->getFilters()->disable('softdeleteable');
        // Get Tag Entity by information
        $tagObject = $em->getRepository('LocalsBestCommonBundle:Tag')
            ->findOneBy(['tag' => $tag, 'objectId' => $id, 'objectType' => $type]);
        // if Tag Entity does not exist
        if (!$tagObject) {
            // Create Tag Entity
            $tagObject = new Tag($tag);
            $tagObject
                ->setObjectType($type)
                ->setObjectId($id)
                ->setCreatedBy($this->getUser());
            // Save Tag Entity
            $em->persist($tagObject);
        }

        // Undelete Tag Entity if was deleted
        if ($tagObject !== null && $tagObject->getDeleted() !== null) {
            $tagObject->setDeleted(null);
        }
        // Attach Tag to Object
        $object->addTag($tagObject);

        if ($tagObject->getId() === null) {
            $em->persist($object);
        }
        // update DB
        $em->flush();

        // Return success JSON
        return new JsonResponse(
            [
                'result' => 'success',
                'tag' => strtolower($tag)
            ]
        );
    }

    /**
     * Detach Tag from object
     *
     * @param Request $request
     * @param $id
     * @param $type
     *
     * @return JsonResponse
     */
    public function removeTagAction(Request $request, $id, $type)
    {
        $em = $this->getDoctrine()->getManager();

        // Get Object Entity
        $object = $this->findOr404($type, array('id' => $id), 'Invalid item');
        // Get tag from request
        $tag = $request->request->get('tag');
        // Get Tag Entity by information
        $tagObject = $em->getRepository("LocalsBestCommonBundle:Tag")
            ->findOneBy(['tag' => $tag, 'objectId' => $id, 'objectType' => $type]);

        if ($tagObject === null) {
            // Return fail JSON
            return new JsonResponse(['result' => 'fail']);
        }
        // Detach Tag entity from Object Entity
        $object->removeTag($tagObject);
        // Remove Tag Entity
        $em->remove($tagObject);
        // Update DB
        $em->flush();
        // Return success JSON
        return new JsonResponse([
            'result' => 'success'
        ]);
            
    }


    /**
     * @param Request $request
     * @param int $id
     * @param string $type
     *
     * @return JsonResponse
     */
    public function addTagNewAction(Request $request, $id, $type)
    {
        $em = $this->getDoctrine()->getManager();

        $object = $this->findOr404("LocalsBestUserBundle:$type", ['id' => $id], 'Invalid item');
        $tagName = $request->request->get('tag');
        $tagName = strtolower($tagName);

        if (!method_exists($object, 'addTag')) {
            return new JsonResponse(['result' => 'fail']);
        }

        $tag = $em->getRepository('LocalsBestUserBundle:Tag')
            ->findOneBy(['name' => $tagName]);

        if ($tag === null) {
            $tag = new Tag2();
            $tag->setName($tagName);

            $em->persist($tag);
            $em->flush();
        }

        if($object->getTags()->contains($tag)) {
            return new JsonResponse([
                'result' => 'success',
                'tag' => strtolower($tagName
                )
            ]);
        }

        $object->addTag($tag);

        $em->flush();

        return new JsonResponse([
            'result' => 'success',
            'tag' => strtolower($tagName
            )
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @param string $type
     *
     * @return JsonResponse
     */
    public function removeTagNewAction(Request $request, $id, $type)
    {
        $em = $this->getDoctrine()->getManager();

        $object = $this->findOr404("LocalsBestUserBundle:$type", ['id' => $id], 'Invalid item');
        $tagName = $request->request->get('tag');
        $tagName = strtolower($tagName);

        $tag = $em->getRepository("LocalsBestUserBundle:Tag")
            ->findOneBy(['name' => $tagName]);

        if ($tag === null) {
            return new JsonResponse(['result' => 'fail']);
        }

        $object->removeTag($tag);
        $em->flush();

        return new JsonResponse([
            'result' => 'success'
        ]);
    }
}
