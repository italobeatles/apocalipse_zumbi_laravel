unit ApiControllersTest;

{$mode objfpc}{$H+}

interface

uses
  fpcunit, testregistry;

type
  TApiControllersTest= class(TTestCase)
  published
    procedure StubCompila;
  end;

implementation

procedure TApiControllersTest.StubCompila;
begin
  AssertTrue('Placeholder de teste para Fano API', True);
end;

initialization
  RegisterTest(TApiControllersTest);
end.

