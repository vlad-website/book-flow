import { Module } from '@nestjs/common';
import { AuthService } from './auth.service';
import { AuthController } from './auth.controller';
import { PrismaModule } from '../prisma/prisma.module';
import { MailModule } from '../mail/mail.module';

@Module({
  imports: [PrismaModule,MailModule], // 👈 ВОТ ЭТО ГЛАВНОЕ
  controllers: [AuthController],
  providers: [AuthService],
})
export class AuthModule {}