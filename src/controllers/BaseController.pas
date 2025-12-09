unit BaseController;

{$mode objfpc}{$H+}

interface

uses
  fano;

type
  TBaseController = class(TInjectableObject, IRequestHandler)
  public
    function handleRequest(const request : IRequest) : IResponse; virtual; abstract;
  end;

implementation

end.

