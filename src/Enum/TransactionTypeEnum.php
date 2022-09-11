<?php

namespace App\Enum;

enum TransactionTypeEnum
{
    // mise en vente avec prix fixe
    case LISTING;

    // achat
    case BUY;

    // mise au enchère
    case AUCTION_TOKEN;

    // mise à jour du prix
    case UPDATE_PRICE;

    // retrait du NFT de la vente
    case WITHDRAW;

    // Echange de NFT et Token
    case NFT_SWAP;

    // mise en vente par système de lottery
    case CREATE;

    // Emission d'une offre
    case OFFER;

    case ADD_MULTI_OFFER;

    /*
     * XOXNO    auctionToken    8
     *          listing         12
     */
}
