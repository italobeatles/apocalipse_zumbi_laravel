unit RelatorioGeralController;

{$mode objfpc}{$H+}

interface

uses
  fano, BaseController;

type
  TRelatorioGeralController = class(TBaseController)
  public
    function handleRequest(const request : IRequest) : IResponse; override;
  end;

implementation

function TRelatorioGeralController.handleRequest(const request : IRequest) : IResponse;
begin
  // TODO: calcular percentuais e médias
  Result := TResponse.create('{"message":"relatorio geral (stub)"}', 200, TStringList.create);
end;

end.

