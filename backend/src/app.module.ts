import { Module } from '@nestjs/common';
import { PrismaModule } from './prisma/prisma.module';
import { AuthModule } from './auth/auth.module';
import { MailModule } from './mail/mail.module';
import { JwtModule } from './jwt/jwt.module';

@Module({
  imports: [PrismaModule, AuthModule, MailModule, JwtModule],
})
export class AppModule {}
