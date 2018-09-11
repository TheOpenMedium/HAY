<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Survey;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SurveyController extends Controller
{
    /**
     * Create a new survey
     *
     * @Route("/{_locale}/survey", name="survey", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function surveyAction()
    {
        $survey = new Survey;
        $survey->setUser($this->getUser());
        $survey->setQuestion("Une petite question");
        $survey->addAnswerOption("Yes", "00FF00");
        $survey->addAnswerOption("No", "FF0000");
        $survey->addAnswerOption("I Don't Know", "CCCCCC");

        $em = $this->getDoctrine()->getManager();
        $em->persist($survey);
        $em->flush();

        return $this->redirectToRoute('survey_show', array(
            'survey' => $survey->getId()
        ));
    }

    /**
     * Render a single survey
     *
     * @param Survey $survey The survey to render
     *
     * @Route("/{_locale}/show/survey/{survey}", name="survey_show", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function showSurveyAction(Survey $survey)
    {
        return $this->render('survey/showSurvey.html.twig', array(
            "survey" => $survey
        ));
    }

    /**
     * CAN'T BE ACCESSED BY URL, ONLY USED FOR THE SURVEY ENTITY.
     */
    public function fetchSurveyUsers(array $answers)
    {
        foreach ($answers as $i => $answerOption) {
            foreach ($answerOption as $j => $value) {
                $answers[$i][$j] = $this->container->get('doctrine')->getRepository(User::class)->find($value);
            }
        }
        return $answers;
    }
}
