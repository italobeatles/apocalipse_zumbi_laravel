Rede Social de Sobrevivência a Zumbis
Desafio Proposto

O mundo como conhecemos caiu em um cenário apocalíptico. Um vírus criado em laboratório está transformando humanos e animais em zumbis, famintos por carne fresca.

Você como membro da Resistência Zumbi (e último sobrevivente que sabe programar), foi escolhido para desenvolver um sistema que tem como objetivo compartilhar recursos entre humanos não infectados.
Requisitos

Seu objetivo é desenvolver uma API REST, que irá armazenar informações dos sobreviventes assim como os recursos que eles tem.

Para que isso seja possível, a API deve atender aos seguintes casos de uso:

    Adicionar sobrevientes à base de dados

    Um sobrevivente deve ter nome, idade, sexo e a sua última localização (latitude, longitude).

    Um sobrevivente também tem um invetário dos recursos que carrega (que precisam ser declarados no momento que este faz o seu registro).

    Os itens que podem ser cadastrado no inventário são:
    Item
    Água
    Comida
    Medicamento
    Munição

    Você saberá mais sobre os pontos quando falarmos sobre as trocas.

    Atualizar localização do sobrevivente

    Um sobrevivente deve conseguir atualizar a sua última localização, guardando o novo par de latitude/longitude no banco de dados (não é preciso manter um histórico das localizações, sobrescrever a última localização é suficiente).

    Marcar sobrevivente como infectado

    Em uma situação caótica como esta, é inevitavel que um sobrevivente talvez seja contaminado, nós precisamos marcar este sobrevivente como infectado.

    Um sobrevivente infectado não pode realizar trocas com outros sobreviventes, não pode acessar/manipular seu inventário e também não pode ser listado nos relatórios (pessoas infectadas estão praticamente mortas de qualquer forma, veja o item relatórios abaixo)

    Um sobrevivente é marcado como infectado quando pelo menos três outros sobreviventes reportarem sua contaminação.

    Quando um sobrevivente é infectado, seu invetário se torna inacessível (eles não podem realizar trocas).

    Sobrevivente não pode Adicionar/Remover items de seus inventários

    Seus recursos devem ser declarados no momento de seu primeiro registro no sistema. Depois disso seu invetário só será atualizado conforme eles fizerem trocas com outros sobreviventes no sistema.

    Veja os itens permitidos no inventário no quadro de items descrito acima.

    Items de Troca

    Sobreviventes podem trocar items entre eles.

    Para isso, eles precisa respeitar a tabela de pontos abaixo, onde o valor de um item é descrito em pontos.
    Item 	Pontos
    Água 	4 pontos
    Comida 	3 pontos
    Medicamento 	2 pontos
    Muniçãp 	1 ponto

    Ambos os sobreviventes, que queiram trocar seus items, precisam oferecer a mesma quantidade de pontos, por exemplo:

    1 Àgua e 1 Medicação (1 x 4 + 1 x 2) podem ser trocadas por 6 munições (6 x 1) ou por 2 comidas (2 x 3).

    As trocas em sí não precisam ser armazenadas, mas os itens precisam ser transferidos de um sobrevivente para o outro.

    Relatórios

    A API precisa oferecer os seguintes relatórios:
        Percentual de sobreviventes infectados;
        Percentual de sobreviventes não infectados;
        Quantidade média de cada tipo de recurso por sobrevivente (e.g. 5 águas por sobrevivente);
        Pontos perdidos por causa dos sobreviventes infectados.
