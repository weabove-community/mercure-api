PROJET MERCURE
==============

# Manipulation d'ajout d'une collection en base donnée

## Collection ERC20
- Télécharger les fichiers de metadata via IPFS
- Mettre les fichiers dans le dossier data/[smartcontract]
- Lancer la commande pour renommer au même format de nom tous les fichiers
```
  php bin/console app:metadata:rename [smartcontract] [extension fichier]
  # exemple : php bin/console app:metadata:rename QmbcXpWty1S2VmxUdGW json
```


# TODO
- Initialiser une API
- API - token : get  by tokenId
- API - token : get tokens by criteria
- Init React project
- afficher l'image du NFT sur sa ficher
- liste les NFT
- ajouter un champ de recherche
- template
- trouver l'algo de ranking de TraitSniper
- ajouter le nombre de trait par NFT
- mettre à null les traits non défini
- afficher le prix des NFT par plateforme
- factoriser l'import de NFT d'une collection

