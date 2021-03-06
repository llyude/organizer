<?php

namespace OrgaperoActivitiesBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use OrgaperoActivitiesBundle\Entity\AbstractActivity;
use OrgaperoActivitiesBundle\Entity\Party;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use OrgaperoActivitiesBundle\Form\PartyType;

class PartyController extends Controller
{
    public function newPartyAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $party = new Party();
        $partyForm = $this->createForm(PartyType::class, $party);
        $partyForm->handleRequest($request);

        if ($partyForm->isSubmitted() && $partyForm->isValid()) {
            $date = $partyForm->get('date')->getData();
            $time = $partyForm->get('time')->getData();
            $datetime = $date . $time;
            $date = \DateTime::createFromFormat('d F, Y H:i', $datetime);
            $party->setDate($date);
            $users = $partyForm->get('listParticipants')->getData();
            $listActivities = $partyForm->get('listActivities')->getData();

//            foreach ($listActivities->toArray() as $activity){
//                $activity = new Activity($activity, $party);
//                $party->addActivities($activity);
//                dump($activity);
//            }
            dump($party);

            $party->addParticipants($users);
            $em = $this->getDoctrine()->getManager();
            $em->persist($party);
            $em->flush();

        }
        return $this->render('OrgaperoActivitiesBundle:Party:new_party.twig.html', array('form' => $partyForm->createView(),)
        );
    }


}
