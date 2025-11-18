<? php
2 // emprestimo_renovar . php
3 require_once ’config / database . php ’;
4 require_once ’config / config . php ’;
5
6 $emprestimo_id = isset ( $_GET [’id ’]) ? ( int ) $_GET [’id ’] : 0;
7
8 if ( $emprestimo_id > 0) {
9
10 try {
11 $db = Database :: getInstance () ;
12 $pdo = $db - > getConnection () ;
13
14 // Buscar dados do emprestimo
$sql = " SELECT * FROM emprestimos WHERE id = :id AND status

= ’Ativo ’";

16 $stmt = $pdo - > prepare ( $sql );
17 $stmt -> execute ([ ’id ’ = > $emprestimo_id ]) ;
18 $emprestimo = $stmt -> fetch () ;
19
20 if (! $emprestimo ) {
21 throw new Exception (" Emprestimo nao encontrado ou ja

foi devolvido ") ;

22 }
23
24 // Verificar se ja esta atrasado
25 if ( $emprestimo [’ data_devolucao_prevista ’] < date (’Y-m-d’))

{

26 throw new Exception (" Nao e possivel renovar emprestimo

em atraso . Realize a devolucao primeiro .");

27 }
28
29 // Renovar emprestimo ( adicionar mais dias )
30 $nova_data = date (’Y-m-d’, strtotime ( $emprestimo [’

data_devolucao_prevista ’] . ’ +’ . PRAZO_EMPRESTIMO_DIAS
. ’ days ’)) ;

31
32 $sql = " UPDATE emprestimos SET data_devolucao_prevista = :

nova_data WHERE id = :id";
33 $stmt = $pdo - > prepare ( $sql );
34 $stmt -> execute ([
35 ’nova_data ’ => $nova_data ,
36 ’id ’ = > $emprestimo_id
37 ]) ;
38
39 $mensagem = " Emprestimo renovado ! Nova data de devolucao : "

. date (’d/m/Y’, strtotime ( $nova_data )) ;

40 header (" Location : emprestimos . php ? msg = renovado & detalhes =" .

urlencode ( $mensagem ) );

41 exit ;
42
43 } catch ( Exception $e ) {
44 header (" Location : emprestimos . php ? erro =" . urlencode ($e - >

getMessage () ));

45 exit ;
46 }
47
48 } else {
49 header (" Location : emprestimos . php ");
50 exit ;
51 }
52 ? >
