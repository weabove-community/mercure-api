<?php

namespace App\Enum;

enum TransactionTypeEnum
{
    // mise au enchère
    case auctionToken;

    // achat
    case buy;

    // mise à jour du prix
    case updatePrice;

    // retrait du NFT de la vente
    case withdraw;

    // mise en vente avec prix fixe
    case listing;

    // Echange de NFT et Token
    case nftSwap;

    // mise en vente par système de lottery
    case create;

    // Emission d'une offre
    case offer;

    case addMultiOffer;

    /*
     * XOXNO    auctionToken    8
     *          listing         12
     */
}
