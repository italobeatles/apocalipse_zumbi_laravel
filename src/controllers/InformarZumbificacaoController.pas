unit InformarZumbificacaoController;

{$mode objfpc}{$H+}

interface

uses
  fano, BaseController;

type
  TInformarZumbificacaoController = class(TBaseController)
  public
    function handleRequest(const request : IRequest) : IResponse; override;
  end;

implementation

function TInformarZumbificacaoController.handleRequest(const request : IRequest) : IResponse;
var responseBody : string;
begin
  // TODO: aplicar regras de contaminação e gravar em tbavisos_zumbificacao
  responseBody := '{"status":"AVISO_REGISTRADO","message":"stub"}';
  Result := TResponse.create(responseBody, 201, TStringList.create);
end;

end.

