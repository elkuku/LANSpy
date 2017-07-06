<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Host;
use AppBundle\Form\HostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HostsController extends Controller
{
    /**
     * @Route("hosts", name="admin.hosts")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction()
    {
        return $this->render(
            'hosts/list.html.twig',
            [
                'hosts' => $this->getDoctrine()
                    ->getRepository(Host::class)
                    ->findBy([], ['name' => 'asc']),
            ]
        );
    }

    /**
     * @Route("edit-host/{id}", name="admin.edit.host")
     * @Security("has_role('ROLE_ADMIN')")
     * @param Host    $host
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Host $host, Request $request)
    {
        if (!$host) {
            throw $this->createNotFoundException('No payment method found');
        }

        $form = $this->createForm(HostType::class, $host);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $this->addFlash('success', 'Host has been saved');

            return $this->redirectToRoute('admin.hosts');
        }

        return $this->render(
            'hosts/form.html.twig',
            [
                'form' => $form->createView(),
                'data' => $host,
            ]
        );
    }
}
