unit SwaggerController;

{$mode objfpc}{$H+}

interface

uses
  Classes, SysUtils, fano, BaseController;

type
  TSwaggerController = class(TBaseController)
  public
    function handleRequest(const request : IRequest) : IResponse; override;
  end;

implementation

function TSwaggerController.handleRequest(const request : IRequest) : IResponse;
var content : TStringList;
begin
  content := TStringList.Create;
  content.LoadFromFile('swagger/openapi.json');
  Result := TResponse.create(content.Text, 200, TStringList.create);
  content.Free;
end;

end.

