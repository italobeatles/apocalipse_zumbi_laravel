Rede Social de Sobrevivência a Zumbis  
Desafio Proposto

O mundo como conhecemos caiu em um cenário apocalíptico. Um vírus criado em laboratório está transformando humanos e animais em zumbis, famintos por carne fresca.

Você como membro da Resistência Zumbi (e último sobrevivente que sabe programar), foi escolhido para desenvolver um sistema que tem como objetivo compartilhar recursos entre humanos não infectados.

Requisitos

Seu objetivo é desenvolver uma API REST, que irá armazenar informações dos sobreviventes assim como os recursos que eles têm.

Para que isso seja possível, a API deve atender aos seguintes casos de uso:

1. Adicionar sobreviventes à base de dados  
   - Um sobrevivente deve ter nome, idade, sexo e a sua última localização (latitude, longitude).  
   - Um sobrevivente também tem um inventório dos recursos que carrega (declarados no momento do registro).
   - Itens permitidos no inventório: Água, Comida, Medicamento, Munição.

2. Atualizar localização do sobrevivente  
   - Deve ser possível atualizar latitude/longitude (sobrescrevendo a última localização).

3. Marcar sobrevivente como infectado  
   - Um sobrevivente infectado não pode fazer trocas, nem manipular inventário e não aparece em relatórios.  
   - É marcado como infectado quando pelo menos três outros sobreviventes reportarem contaminação.  
   - Quando infectado, seu inventório se torna inacessível.

4. Sobrevivente não pode adicionar/remover itens livremente  
   - Recursos são declarados no primeiro registro. Depois disso o inventário só muda por trocas.

5. Itens de troca  
   - Tabela de pontos: Água (4), Comida (3), Medicamento (2), Munição (1).  
   - Trocas precisam ter a mesma soma de pontos (ex.: 1 Água + 1 Medicamento = 6 pontos pode trocar por 6 Munições ou 2 Comidas).
   - Trocas não precisam ser armazenadas, mas o inventário de cada sobrevivente deve ser atualizado.

6. Relatórios  
   - Percentual de sobreviventes infectados;  
   - Percentual de sobreviventes não infectados;  
   - Quantidade média de cada tipo de recurso por sobrevivente (ex.: 5 águas por sobrevivente);  
   - Pontos perdidos por causa dos sobreviventes infectados.
