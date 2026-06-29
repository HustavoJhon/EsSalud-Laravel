import { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'pe.essalud.app',
  appName: 'EsSalud',
  webDir: 'public',
  server: {
    // Desarrollo: descomenta y pon la IP local donde corre Laravel
    // url: 'http://192.168.x.x:8082',
    // Producción: cambia por tu dominio con HTTPS
    // url: 'https://tudominio.com',
    // androidScheme: 'https',
  },
  plugins: {
    SplashScreen: {
      launchShowDuration: 2000,
      backgroundColor: '#003d7a',
      androidSplashResourceName: 'splash',
      androidScaleType: 'CENTER_CROP',
    },
  },
  android: {
    buildOptions: {
      keystorePath: undefined,
      keystoreAlias: undefined,
      keystorePassword: undefined,
      keystoreAliasPassword: undefined,
    },
  },
};

export default config;
