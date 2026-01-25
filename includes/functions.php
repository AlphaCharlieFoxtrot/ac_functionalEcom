<?php

function debug($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

function statutCommande($statut){
    $etats = [
        "en_attente" => "En attente",
        "en_preparation" => "En préparation",
        "expediee" => "Expédiée",
        "livree" => "Livrée",
        "annulee" => "Annulée"
    ];

    if(isset($etats[$statut])){
        return $etats[$statut];
    }else{
        return $statut;
    };  
}


function sqlValue($pdo, $query){
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchColumn();
}

function sqlFetchAll($pdo, $query){
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
