<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class AguaEquipe extends CascataAppModel
{
    var $name = 'AguaEquipe';
    
    var $hasOne = array('Cascata.AguaProfessor');

    var $actsAs = array('Cascata.AguaCascata');

    function afterFind($results, $primary)
    {
        if ($primary) {
            foreach ($results as $key => $val)
            {
                $results[$key]['AguaProfessor']['nome'] = $results[$key]['AguaProfessor']['nome'] . " Equipe";
            }
        }
        return $results;
    }

    function afterFindCascata($results)
    {
        foreach ($results as $key => $val)
        {
            //caso em que é hasMany, não sei se esse caso eu preciso tratar aqui
            //ou no behaviorCascata
            if (isset($results[$key]['AguaEquipe']['AguaProfessor'])) {
                if (isset($results[$key]['AguaEquipe']['AguaProfessor'][0])){
                    foreach ($results[$key]['AguaEquipe']['AguaProfessor'] as $chave_prof => $prof)
                        $results[$key]['AguaEquipe']['AguaProfessor'][$chave_prof]['nome'] = $results[$key]['AguaEquipe']['AguaProfessor'][$chave_prof]['nome'] . " Equipe";
                } else
                $results[$key]['AguaEquipe']['AguaProfessor']['nome'] = $results[$key]['AguaEquipe']['AguaProfessor']['nome'] . " Equipe";
            }
        }

        return $results;
    }

    function pegaTodas(){
        return $this->find('all');
    }
}

?>