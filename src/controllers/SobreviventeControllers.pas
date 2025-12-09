unit SobreviventeControllers;

{$mode objfpc}{$H+}

interface

uses
  SysUtils, fano, BaseController;

type
  TSobreviventeListController = class(TBaseController)
  public
    function handleRequest(const request : IRequest) : IResponse; override;
  end;

  TSobreviventeShowController = class(TBaseController)
  public
    function handleRequest(const request : IRequest) : IResponse; override;
  end;

  TSobreviventeCreateController = class(TBaseController)
  public
    function handleRequest(const request : IRequest) : IResponse; override;
  end;

  TSobreviventeUpdateController = class(TBaseController)
  public
    function handleRequest(const request : IRequest) : IResponse; override;
  end;

  TSobreviventeDeleteController = class(TBaseController)
  public
    function handleRequest(const request : IRequest) : IResponse; override;
  end;

implementation

function TSobreviventeListController.handleRequest(const request : IRequest) : IResponse;
var responseBody : string;
begin
  // TODO: implementar consulta ao banco (tbsobreviventes)
  responseBody := '{"message":"listar sobreviventes (stub)"}';
  Result := TResponse.create(responseBody, 200, TStringList.create);
end;

function TSobreviventeShowController.handleRequest(const request : IRequest) : IResponse;
var responseBody : string;
begin
  // TODO: buscar sobrevivente por id e retornar JSON
  responseBody := '{"message":"detalhe sobrevivente (stub)"}';
  Result := TResponse.create(responseBody, 200, TStringList.create);
end;

function TSobreviventeCreateController.handleRequest(const request : IRequest) : IResponse;
var responseBody : string;
begin
  // TODO: validar payload, inserir em tbsobreviventes e tbsobreviventes_recursos
  responseBody := '{"status":"OK","message":"sobrevivente criado (stub)"}';
  Result := TResponse.create(responseBody, 201, TStringList.create);
end;

function TSobreviventeUpdateController.handleRequest(const request : IRequest) : IResponse;
var responseBody : string;
begin
  // TODO: bloquear zumbi, atualizar campos permitidos
  responseBody := '{"status":"OK","message":"sobrevivente atualizado (stub)"}';
  Result := TResponse.create(responseBody, 200, TStringList.create);
end;

function TSobreviventeDeleteController.handleRequest(const request : IRequest) : IResponse;
var responseBody : string;
begin
  // TODO: remover sobrevivente e relacionamentos
  responseBody := '{"status":"OK","message":"sobrevivente removido (stub)"}';
  Result := TResponse.create(responseBody, 200, TStringList.create);
end;

end.

