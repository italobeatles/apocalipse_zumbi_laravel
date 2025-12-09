unit InventarioControllers;

{$mode objfpc}{$H+}

interface

uses
  fano, BaseController;

type
  TInventarioListController = class(TBaseController)
  public
    function handleRequest(const request : IRequest) : IResponse; override;
  end;

  TInventarioShowController = class(TBaseController)
  public
    function handleRequest(const request : IRequest) : IResponse; override;
  end;

  TInventarioTrocaController = class(TBaseController)
  public
    function handleRequest(const request : IRequest) : IResponse; override;
  end;

implementation

function TInventarioListController.handleRequest(const request : IRequest) : IResponse;
begin
  Result := TResponse.create('{"message":"inventario list (stub)"}', 200, TStringList.create);
end;

function TInventarioShowController.handleRequest(const request : IRequest) : IResponse;
begin
  Result := TResponse.create('{"message":"inventario show (stub)"}', 200, TStringList.create);
end;

function TInventarioTrocaController.handleRequest(const request : IRequest) : IResponse;
begin
  // TODO: validar pontuação e estoque, atualizar inventários
  Result := TResponse.create('{"status":"sucesso","message":"troca stub"}', 200, TStringList.create);
end;

end.

