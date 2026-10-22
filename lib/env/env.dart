// lib/env/env.dart
import 'package:envied/envied.dart';

part 'env.g.dart';

@Envied(path: '.env')
abstract class Env {
  @EnviedField(varName: 'API')
  static const String api = _Env.api;
  @EnviedField(varName: 'GMAPKEY')
  static const String gMapKey = _Env.gMapKey;
  @EnviedField(varName: 'SUPABASEURL')
  static const String supabaseUrl = _Env.supabaseUrl;
  @EnviedField(varName: 'SUPABASEKEY')
  static const String supabaseKey = _Env.supabaseKey;
  @EnviedField(varName: 'GMAPURL')
  static const String gMapUrl = _Env.gMapUrl;
  @EnviedField(varName: 'GSTATICMAP')
  static const String gStaticMap = _Env.gStaticMap;
}
