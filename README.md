# sortir-dot-com
![Sortir.com](src_assets/exports/logo_sortir.png?raw=true "Sortir")  

## site pour gérer des sorties

**INSTALLATION**  

1 clone projet dans [wampdir]/www/
  
2 composer install  

3 php bin/console doctrine:database:create   

4 php bin/console doctrine:schema:update --force    

5 go to phpStorm > Create new Project from existing Files ..[wampdir]/www/sortir-dot-com/    
5.2 activer le module symfony en bas a droite de php storm si c'est pas fait automatiquement    

6 go to http://localhost/sortir-dot-com/public/

7 connection en mode admin avec admin:admin


**ROUTES** 
 
  | Nom | Route | OK | admin  | user |
  | ---- | ---- | ---- | ---- | ---- |   
  | home                 |      /                             |   | ❌ | ❌     
  | main_home            |      /website                      |   | ✅ | ✅       
  | sortie_ajouter       |      /website/sortie/ajouter       |   | ✅ | ✅         
  | sortie_detail        |      /website/sortie/{id}          |   | ✅ | ✅        
  | sortie_modifier      |      /website/sortie/modifier/{id} |   | ✅ | ✅      
  | sortie_annuler       |      /website/sortie/annuler/{id}  |   | ✅ | ✅        
  | update_bdd           |      /update/bdd                   |   | ✅ | ❌   
  | register             |      /register/{id}                |   | ✅ | ❌  
  | login                |      /login                        | ✅ | ❌ | ❌ 
  | logout               |      /logout                       | ✅ | ❌ | ❌       
  | profile              |      /profile                      | ❌ | ✅ | ❌       
  | ville_liste          |      /website/ville/               |   | ✅ | ❌        
